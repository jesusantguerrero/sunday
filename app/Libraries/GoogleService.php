<?php
namespace App\Libraries;

use App\Jobs\ProcessCalendar;
use App\Jobs\ProcessGmail;
use App\Models\User;
use App\Models\Automation;
use App\Models\Integration;
use Exception;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Gmail;

class GoogleService
{

    public static function getTokens($userId) {
        return User::find($userId);
    }

    public static function getConfigPath() {
        return base_path(config("integrations.google.credentials_path"));
    }

    public static function setTokens($data, $user, $integrationId = null) {
        if (!$integrationId && $_GET['code']) {

            $client = new GoogleClient([ 'client_id' => config('integrations.google.client_id')]);
            $client->setAuthConfig(self::getConfigPath());
            $client->setAccessType('offline');
            $userIdToken = $_GET['code'];
            $tokenResponse = $client->fetchAccessTokenWithAuthCode($userIdToken);
            $integration = Integration::where([
                'user_id' => $user->id,
                'team_id' => $user->current_team_id,
                'name' => 'Google'
            ])->first();
            $googleUser = $client->verifyIdToken($tokenResponse["id_token"]);
            if ($googleUser['email'] == $user->email) {
                $integration->token = encrypt($tokenResponse['access_token']);
                $integration->save();
                session(['g_token', json_encode($tokenResponse)]);
                return;
            }
            throw new Exception("Error obtaining the token" . $googleUser['email']);
        } else if ($integrationId) {
            $integration = Integration::find($integrationId);
            $integration->token = encrypt($data->refresh_token);
            session(['g_token', json_encode($data)]);
            return;
        };
    }

    public static function getClient($integrationId) {
        $integration = Integration::find($integrationId);
        $client = new GoogleClient();
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
                ],
                $integration->user,
                $integrationId);
                $client->setAccessToken($accessToken);
            }
        }

        return $client;
    }

    public static function storeIntegration($data, $user) {
        Integration::updateOrCreate([
            "team_id" => $user->current_team_id,
            "user_id" => $user->id,
            "name" => $data->service_name,
            "automation_service_id" => $data->service_id
        ], [
            "hash" => $user->email
        ]);
    }

    public static function requestAccessToken($data, $user) {
        $client = new GoogleClient([
            "client_id" => config('integrations.google.client_id')
        ]);
        $client->addScope([
            Gmail::GMAIL_READONLY,
            Calendar::CALENDAR_READONLY
        ]);
        $client->setRedirectUri(config('app.url') . "/services/accept-oauth");
        $client->setAccessType('offline');
        $client->setLoginHint($user->email);
        $client->setApprovalPrompt('force');
        $client->setIncludeGrantedScopes(true);

        $authUrl = $client->createAuthUrl();
        if ($authUrl) {
            self::storeIntegration($data, $user);
        }
        return $authUrl;
    }

    // services
    public static function createItemFromCalendar($automationId, $afterResponse = null) {
        $automation = Automation::find($automationId);
        echo "$automation->name $automation->id \n";
        $method = $afterResponse ? "dispatchAfterResponse" : "dispatch";
        ProcessCalendar::$method($automation);
        return true;
    }

    public static function listCalendars(int $integrationId) {
        $client = self::getClient($integrationId);
        $service = new Calendar($client);
        return $service->calendarList->listCalendarList();
    }

    public static function createItemFromGmail($automationId, $afterResponse = null) {
       $automation = Automation::find($automationId);
       echo "$automation->name $automation->id \n";
       $method = $afterResponse ? "dispatchAfterResponse" : "dispatch";
       ProcessGmail::$method($automation);
       return true;
    }
}
