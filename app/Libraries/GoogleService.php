<?php
namespace App\Libraries;

use App\Jobs\ProcessCalendar;
use App\Jobs\ProcessGmail;
use App\Models\User;
use App\Models\Automation;
use Google_Service_Gmail;
use Google_Service_Calendar;
use Google_Client;

class GoogleService
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

        $client = self::getClient($userId);
        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';
        $results = $service->events->listEvents($calendarId, [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c')
        ]);
        $events = $results->getItems();
        ProcessCalendar::dispatch($automation, $events);
    }

    public static function createItemFromMessage(int $userId, int $automationId) {
        $automation = Automation::find($automationId);
        $automationConfig = json_decode($automation->config);

        $client = self::getClient($userId);
        $service = new Google_Service_Gmail($client);
        $results = $service->users_threads->listUsersThreads("me", ['maxResults' => 50, 'q' => "$automationConfig->condition <$automationConfig->value>"]);
        ProcessGmail::dispatch($automation, $results->getThreads());
    }
}
