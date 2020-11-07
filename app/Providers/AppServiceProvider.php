<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Inertia::share('users', function (Request $request) {
            if ($user = $request->user()) {
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
        });

        JsonResource::withoutWrapping();

    }
}
