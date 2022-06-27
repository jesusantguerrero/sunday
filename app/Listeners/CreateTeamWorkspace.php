<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Jetstream\Events\TeamCreated;
use App\Models\Workspace;

class CreateTeamWorkspace
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TeamCreated $event)
    {
        $team = $event->team;
        $workspace = new Workspace();
        $workspace->user_id =  $team->user_id;
        $workspace->team_id = $team->id;
        $workspace->name = "Main Workspace";
        $workspace->description = "Main Workspace";
        $workspace->bg_color = "c084fe";
        $workspace->save();
        $user = User::find($team->user_id);
        $user->current_workspace_id = $workspace->id;
        $user->save();
    }
}
