<?php

namespace App\Actions\Sunday;

use App\Models\Automation;
use App\Models\Board;
use App\Models\Item;
use DateTime;
use Google_Service_Calendar_Event;

class CreateTaskFromCalendar
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param  Automation  $automation
     * @param  Google_Calendar_Events  $calendarEvents
     * @return void
     */
    public static function create(Automation $automation, Array $calendarEvents)
    {
        foreach ($calendarEvents as $event) {
            $board = Board::find($automation->board_id);
            $stage = $board->stages[0];
            $date = new DateTime($event->start->dateTime);
            $item = [
                'title' => $event->getSummary(),
                'board_id' => $stage->board_id,
                'stage_id' => $stage->id,
                'user_id' => $stage->user_id,
                'team_id' => $stage->team_id,
                'fields' => [
                    ['name' => 'date', 'type'=> 'date', 'value' => $date->format('Y-m-d')],
                    ['name' => 'time', 'type' => 'time', 'value' => $date->format('H:i')],
                    ['name' => 'itemType', 'value' => 'event', 'hide' => 1],
                    ['name' => 'automation_id', 'value' => $automation->id, 'hide' => 1],
                ]
            ];
            Item::createEvent($item);
        }
    }
}
