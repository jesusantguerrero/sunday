<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Models\Team;
use Closure;
use Dotenv\Store\File\Reader;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HandleInertiaRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->share($request);
        return $next($request);
    }

    public function share(Request $request)
    {
        $user = $request->user();
        Inertia::share(array_filter([
            'users' => function () use ($user) {
                if ($user) {
                    $currentTeamId = $user->current_team_id;
                    if (!$currentTeamId) {
                        $team =Team::where([
                            "user_id" => $user->id,
                            "personal_team" => 1
                        ])->limit(1)->get()[0];
                    } else {
                        $team = Team::find($currentTeamId);
                    }
                    return  $team->allUsers();
                }
            },
            'settings' => function () use ($user) {
                return Setting::getFormatted([
                    "user_id" => $user->id,
                    "team_id" => $user->current_team_id
                ]);
            }
        ]));
    }
}
