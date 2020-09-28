<?php
namespace App\Libraries;
use App\Models\User;
use App\Models\Board;
use App\Models\Automation;
use Google_Service_Gmail;
use Google_Client;
use PhpMimeMailParser\Parser;

class GmailService
{

    public static function getTokens($userId) {
        return User::find($userId);
    }

    public static function setTokens($data, $userId) {
        $client = new Google_Client();
        $client->setAuthConfig(app_path().'\..\credentials.json');
        $client->setRedirectUri('http://localhost:8080');
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

    public static function createItemFromMessage($userId, $automationId) {
        $automation = Automation::find($automationId);
        $track = json_decode($automation->track, true);
        $automationConfig = json_decode($automation->config);
        $track['historyId'] = $track['historyId'] ?? 0;

        $client = self::getClient($userId);
        $service = new Google_Service_Gmail($client);
        $user = 'me';
        $results = $service->users_threads->listUsersThreads($user, ['maxResults' => 50, 'q' => "$automationConfig->condition <$automationConfig->value>"]);
        $messages = [];

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
                $stage->items()->create([
                    'title' => $mail['subject'],
                    'board_id' => $stage->board_id,
                    'user_id' => $stage->user_id,
                    'team_id' => $stage->team_id,
                ]);
                $messages[] = $mail;

            }
        };

        return $messages;
    }

    public static function parseEmail($raw) {
        $switched = str_replace(['-', '_'], ['+', '/'], $raw['raw']);
        $raw = base64_decode($switched);
        return (new Parser)->setText($raw);
    }
}
