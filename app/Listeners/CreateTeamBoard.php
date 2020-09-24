<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Jetstream\Events\TeamCreated;
use App\Models\Board;

class CreateTeamBoard
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
        $board = new Board();
        $board->user_id =  $team->user_id;
        $board->team_id = $team->id;
        $board->name = "$team->name Board";
        $board->save();
        $board->createMainStage();
    }
}
