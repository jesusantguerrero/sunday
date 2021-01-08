<?php

namespace App\Actions\Sunday;

use App\Libraries\GoogleService;
use App\Models\Automation;
use App\Models\Board;
use App\Models\Item;
use App\Models\Stage;
use DateTime;
use Google_Service_Calendar;
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
    public static function create(Automation $automation)
    {


        $client = GoogleService::getClient($automation->integration_id);
        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';
        $results = $service->events->listEvents($calendarId, [
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c')
        ]);
        $calendarEvents = $results->getItems();

        foreach ($calendarEvents as $event) {
            $config = json_decode($automation->config);
            $board = Board::find($automation->board_id);
            $stage = !empty($config->stage_id) ? Stage::find($config->stage_id) : $board->stages[0];
            $date = new DateTime($event->start->dateTime);
            $endDate = new DateTime($event->end->dateTime);

            $item = [
                'title' => $event->getSummary(),
                'board_id' => $stage->board_id,
                'stage_id' => $stage->id,
                'user_id' => $stage->user_id,
                'team_id' => $stage->team_id,
                'resource_id' => $event->getId(),
                'resource_origin' => 'calendar',
                'fields' => [
                    ['name' => 'date', 'type'=> 'date', 'value' => $date->format('Y-m-d')],
                    ['name' => 'time', 'type' => 'time', 'value' => $date->format('H:i')],
                    ['name' => 'due_date', 'type'=> 'date', 'value' => $endDate->format('Y-m-d')],
                    ['name' => 'end_time', 'type' => 'time', 'value' => $endDate->format('H:i'), 'hide' => true],
                    ['name' => 'itemType', 'value' => 'event', 'hide' => true],
                    ['name' => 'automation_id', 'value' => $automation->id, 'hide' => true],
                ]
            ];

            Item::createEvent($item, [
                'resource_id' => $item['resource_id'],
                "resource_origin" => 'calendar',
                "stage_id" => $item['stage_id']
            ]);
        }
    }
}
