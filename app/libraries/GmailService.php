<?php
namespace App\Libraries;

use App\Models\User;
use App\Models\Board;
use App\Models\Automation;
use App\Models\Item;
use Carbon\Carbon;
use DateTime;
use Google_Service_Gmail;
use Google_Service_Calendar;
use Google_Client;
use PhpMimeMailParser\Parser as EmailParser;

class GmailService
{

    public static function getTokens($userId) {
        return User::find($userId);
    }

    public static function setTokens($data, $userId) {
        $client = new Google_Client();
        $client->setAuthConfig(app_path().'\..\credentials.json');
        $client->setRedirectUri('http://localhost:8000');
        if ($data->code) {
            $tokenResponse = $client->fetchAccessTokenWithAuthCode($data->code);
            session(['g_token', json_encode($tokenResponse)]);
        } else {
            $tokenResponse = $data;
        }
        $user = User::find($userId);
        $user->token = encrypt($tokenResponse['refresh_token']);
        $user->save();
        return $tokenResponse;
    }

    public static function getClient( $userId) {
        $user = User::find($userId);
        $client = new Google_Client();
        $client->setAuthConfig(app_path().'\..\credentials.json');
        if (!$accessToken = session('g_token')) {
            $accessToken = $client->fetchAccessTokenWithRefreshToken(decrypt($user->token));
        }

        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            if ($refreshToken = $client->refreshToken(decrypt($user->token))) {
                $accessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                self::setTokens((object) [
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken
                ], $userId);
                $client->setAccessToken($accessToken);
            }
        }

        return $client;
    }

    public static function listenAutomations() {
        $automations = Automation::all();
        foreach ($automations as $automation) {
            $service = $automation->recipe->name;
            echo "$automation->name";
            self::$service($automation->user_id, $automation->id);
        }

    }

    public static function createItemFromCalendar(int $userId, int $automationId) {
        $automation = Automation::find($automationId);
        $track = json_decode($automation->track, true);
        $automationConfig = json_decode($automation->config);
        $track['historyId'] = $track['historyId'] ?? 0;

        $client = self::getClient($userId);
        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';
        $results = $service->events->listEvents($calendarId, [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
            'timeMax' => Carbon::tomorrow()->format('c')
        ]);
        $events = $results->getItems();
        foreach ($events as $event) {
            $board = Board::find($automation->board_id);
            $stage = $board->stages[0];
            $date = new DateTime($event->start->dateTime);
            echo "{$date->format('H:i')} \n";
            $item = [
                'title' => $event->getSummary(),
                'board_id' => $stage->board_id,
                'stage_id' => $stage->id,
                'user_id' => $stage->user_id,
                'team_id' => $stage->team_id,
                'fields' => [
                    ['name' => 'date', 'type'=> 'date', 'value' => $date->format('Y-m-d')],
                    ['name' => 'time', 'type' => 'time', 'value' => $date->format('H:i')],
                    ['name' => 'itemType', 'value' => 'event', 'hide' => 1],
                    ['name' => 'automation_id', 'value' => $automationId, 'hide' => 1],
                ]
            ];
            Item::createEvent($item);
        }
    }

    public static function createItemFromMessage(int $userId, int $automationId) {
        $automation = Automation::find($automationId);
        $track = json_decode($automation->track, true);
        $automationConfig = json_decode($automation->config);
        $track['historyId'] = $track['historyId'] ?? 0;

        $client = self::getClient($userId);
        $service = new Google_Service_Gmail($client);
        $user = 'me';
        $results = $service->users_threads->listUsersThreads($user, ['maxResults' => 50, 'q' => "$automationConfig->condition <$automationConfig->value>"]);
        $messages = [];

        try {
        forEach($results->getThreads() as $index => $thread) {
                $theadResponse = $service->users_threads->get($user, $thread->id, ['format' => 'MINIMAL']);
                $message = $theadResponse->getMessages()[0];
                if ($message && ($message->historyId > $track['historyId'])) {
                    $raw = $service->users_messages->get($user, $message->id, ['format' => 'raw']);
                    $parser = self::parseEmail($raw);

                    $body = $parser->getMessageBody('html');
                    $mail = [
                        'index' => $index,
                        'subject' => $parser->getHeader('subject'),
                        'id' => $message->id,
                        'threadId' => $message->threadId,
                        'historyId' => $message->historyId
                    ];

                    if ($index == 0) {
                        $automation->track = json_encode($mail);
                        $automation->save();
                    }

                    $mail['message'] = $body;
                    $board = Board::find($automation->board_id);
                    $stage = $board->stages[0];
                    $item = [
                        'title' => $mail['subject'],
                        'board_id' => $stage->board_id,
                        'user_id' => $stage->user_id,
                        'team_id' => $stage->team_id,
                    ];
                    $stage->items()->create($item);
                    dump($item['title']);
                    $messages[] = $message;

                }

            };
        } catch (\Exception $e) {
            dump($index);
        }

        return $messages;
    }

    public static function parseEmail($raw) {
        $switched = str_replace(['-', '_'], ['+', '/'], $raw['raw']);
        $raw = base64_decode($switched);
        return (new EmailParser)->setText($raw);
    }
}
