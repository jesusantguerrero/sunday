<?php
namespace App\Libraries;

use App\Jobs\ProcessCalendar;
use App\Jobs\ProcessGmail;
use App\Models\User;
use App\Models\Automation;
use App\Models\Integration;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Sheets;
use Google_Service_Sheets_Sheet;

class GoogleService
{

    public static function getTokens($userId) {
        return User::find($userId);
    }
    public static function getConfigPath() {
        return base_path(config("integrations.google.credentials_path"));
    }

    public static function setTokens($data, $userId, $integrationId = null) {
        $client = new Google_Client();
        $client->setAuthConfig(self::getConfigPath());
        $client->setRedirectUri(config('app.url'));
        if ($data->code) {
            $tokenResponse = $client->fetchAccessTokenWithAuthCode($data->code);
            session(['g_token', json_encode($tokenResponse)]);
        } else {
            $tokenResponse = $data;
        }
        $user = user::find($userId);
        $integration = new Integration();
        $integration->team_id = $user->current_team_id;
        $integration->user_id = $user->id;
        $integration->name = $data->service_name;
        $integration->automation_service_id = $data->service_id;
        dd($tokenResponse);
        $integration->token = encrypt($tokenResponse['refresh_token']);
        $integration->hash = $data->user;
        $integration->save();
        return $tokenResponse;
    }

    public static function getClient($integrationId) {
        $integration = Integration::find($integrationId);
        $client = new Google_Client();
        $client->setAuthConfig(self::getConfigPath());
        if (!$accessToken = session('g_token')) {
            $accessToken = $client->fetchAccessTokenWithRefreshToken(decrypt($integration->token));
        }

        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            if ($refreshToken = $client->refreshToken(decrypt($integration->token))) {
                $accessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                self::setTokens((object) [
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken
                ], $integration->user_id, $integrationId);
                $client->setAccessToken($accessToken);
            }
        }

        return $client;
    }

    public static function createItemFromCalendar($automationId, $afterResponse = null) {
        $automation = Automation::find($automationId);
        echo "$automation->name $automation->id \n";
        $method = $afterResponse ? "dispatchAfterResponse" : "dispatch";
        ProcessCalendar::$method($automation);
        return true;
    }

    public static function listCalendars(int $integrationId) {
        $client = self::getClient($integrationId);
        $service = new Google_Service_Calendar($client);
        return $service->calendarList->listCalendarList();
    }

    public static function createItemFromGmail($automationId, $afterResponse = null) {
       $automation = Automation::find($automationId);
       echo "$automation->name $automation->id \n";
       $method = $afterResponse ? "dispatchAfterResponse" : "dispatch";
       ProcessGmail::$method($automation);
       return true;
    }

    public static function getSheetsService($integrationId) {
        $client = GoogleService::getClient($integrationId);
        $service = new Google_Service_Sheets($client);
        return $service;
    }

    public static function getSheetService($integrationId) {
        $client = GoogleService::getClient($integrationId);
        $service = new Google_Service_Sheets_Sheet($client);
        return $service;
    }
}
