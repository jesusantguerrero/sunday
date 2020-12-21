<?php
namespace App\Libraries;

use App\Jobs\ProcessCalendar;
use App\Jobs\ProcessGmail;
use App\Models\User;
use App\Models\Automation;
use App\Models\Integration;
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
        $client->setRedirectUri('http://localhost:8080');
        if ($data->code) {
            $tokenResponse = $client->fetchAccessTokenWithAuthCode($data->code);
            session(['g_token', json_encode($tokenResponse)]);
        } else {
            $tokenResponse = $data;
        }
        $user = User::find($userId);
        $integration = new Integration();
        $integration->team_id = $user->current_team_id;
        $integration->user_id = $user->id;
        $integration->name = $data->service_name;
        $integration->automation_service_id = $data->service_id;
        $integration->token = encrypt($tokenResponse['refresh_token']);
        $integration->hash = $data->user['wt']['cu'];
        $integration->save();
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
