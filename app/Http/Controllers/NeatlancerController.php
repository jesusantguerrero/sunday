<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class NeatlancerController {

    public function connect() {
        request()->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => '98e6222a-6bd7-4f85-a4a6-01931fd0e3fe',
            'redirect_uri' => config('app.url') .'/oauth/accept',
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            // 'prompt' =>  "consent"
            // 'prompt' => '', // "none", "consent", or "login"
        ]);

        return redirect(config('app.sso_url') . '/oauth/authorize?'.$query);
        // dd(Socialite::driver('laravelpassport'));
    }

    public function accept() {
            // $state = request()->session()->pull('state');

            // throw_unless(
            //     strlen($state) > 0 && $state === request()->state,
            //     InvalidArgumentException::class,
            //     'Invalid state value.'
            // );

            // $response = Http::asForm()->post(config('app.sso_url') . '/oauth/token', [
            //     'grant_type' => 'authorization_code',
            //     'client_id' => config('integrations.neatlancer.client_id'),
            //     'client_secret' => config('integrations.neatlancer.client_secret'),
            //     'redirect_uri' => config('app.url') .'/oauth/accept',
            //     'code' => request()->code,
            // ]);

            // return $response->json();
        $user = Socialite::driver('laravelpassport')->user();

        $user = User::updateOrCreate([
            'neatlancer_id' => $user->id,
        ], [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'neatlancer_token' => $user->token,
            'neatlancer_refresh_token' => $user->refreshToken,
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }
}
