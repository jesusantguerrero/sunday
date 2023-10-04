<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class NeatlancerController {

    public function connect() {
        // request()->session()->put('state', $state = Str::random(40));

        // $query = http_build_query([
        //     'client_id' => '98e6222a-6bd7-4f85-a4a6-01931fd0e3fe',
        //     'redirect_uri' => config('app.url') .'/oauth/accept',
        //     'response_type' => 'code',
        //     'scope' => '',
        //     'state' => $state,
        //     // 'prompt' =>  "consent"
        //     // 'prompt' => '', // "none", "consent", or "login"
        // ]);

        // return redirect(config('app.sso_url') . '/oauth/authorize?'.$query);
        return Socialite::driver('laravelpassport')->redirect();
    }

    public function accept() {
        $accessInfo = Socialite::driver('laravelpassport')->user();

        $guzzle = new Client(['base_uri' => config('app.sso_url')]);

        $raw_response = $guzzle->get('/api/current-user', [
            'headers' => [ 'Authorization' => 'Bearer ' . $accessInfo->token ],
        ]);

        $userInfo = json_decode($raw_response->getBody()->getContents());

        $user = User::updateOrCreate([
            'neatlancer_id' => $userInfo->id,
        ], [
            'name' => $userInfo->name,
            'email' => $userInfo->email,
            // 'avatar' => $userInfo?->avatar,
            'neatlancer_token' => $accessInfo->token,
            'neatlancer_refresh_token' => $accessInfo->refreshToken,
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }
}
