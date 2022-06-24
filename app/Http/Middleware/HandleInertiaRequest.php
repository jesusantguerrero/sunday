<?php

namespace App\Http\Middleware;

use App\Http\Resources\BoardResource;
use App\Models\Board;
use App\Models\BoardTemplate as ModelsBoardTemplate;
use App\Models\BoardType as ModelsBoardType;
use App\Models\Setting;
use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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
            'boards' => BoardResource::collection(Board::where([
                'team_id' => $user->current_team_id,
                'user_id' => $user->id,
                'board_type_id' => 1
            ])->get()),
            'boardTypes' => ModelsBoardType::all(),
            'boardTemplates' => ModelsBoardTemplate::all(),
            'settings' => function () use ($user) {
                return Setting::getFormatted([
                    "user_id" => $user->id,
                    "team_id" => $user->current_team_id
                ]);
            }
        ]));
    }
}
