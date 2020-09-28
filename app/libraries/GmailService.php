<?php
namespace App\Libraries;
use App\Models\User;
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

    public static function getMessages($userId) {
        $client = self::getClient($userId);
        $service = new Google_Service_Gmail($client);
        $user = 'me';
        $results = $service->users_messages->listUsersMessages($user, ['maxResults' => 50]);
        $messages = [];

        forEach($results->getMessages() as $message) {
            $raw = $service->users_messages->get($user, $message->id, ['format' => 'raw']);
            $switched = str_replace(['-', '_'], ['+', '/'], $raw['raw']);
            $raw = base64_decode($switched);
            $parser = (new Parser)->setText($raw);
            $headerFrom = array_map(function ($to)
            {
                return $to['address'];
            }, $parser->getAddresses('to'));
            if (\str_contains(implode(' ',$headerFrom), "all.oorden@abits.com" )) {
                $mail = [
                    'subject' => $parser->getHeader('subject'),
                    'message' => $parser->getMessageBody('html'),
                    'id' => $message->id,
                    'threadId' => $message->threadId
                ];
                $messages[] = $mail;
            }
        };

        return $messages;
    }
}
