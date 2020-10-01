<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Team;

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
            if ($request->user()) {
                $team = Team::find($request->user()->current_team_id);
                return  $team->allUsers();
            }
        });

    }
}
