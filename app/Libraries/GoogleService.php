<?php
namespace App\Libraries;

use App\Jobs\ProcessCalendar;
use App\Jobs\ProcessGmail;
use App\Models\User;
use App\Models\Automation;
use Facade\FlareClient\Http\Response;
use Google_Client;
use Google_Service_Calendar;
use GuzzleHttp\Psr7\Request;

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
            echo "$automation->name \n";
            $service = $automation->recipe->name;
            self::$service($automation->id);
        }

    }

    public static function createItemFromCalendar(int $automationId) {
        $automation = Automation::find($automationId);
        ProcessCalendar::dispatch($automation);
    }

    public static function listCalendars(int $userId) {
        $client = self::getClient($userId);
        $service = new Google_Service_Calendar($client);
        return $service->calendarList->listCalendarList();
    }

    public static function createItemFromMessage(int $automationId) {
        $automation = Automation::find($automationId);
        ProcessGmail::dispatch($automation);
    }
}
