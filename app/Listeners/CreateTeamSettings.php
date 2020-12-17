<?php

namespace App\Listeners;

use App\Models\Setting;
use Laravel\Jetstream\Events\TeamCreated;

class CreateTeamSettings
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
        $settings = [
            [
                "name" => "promodoro_template",
                "value" => json_encode(["session","longBreak"])
            ],
            [
                "name" => "promodoro_modes",
                "value" => json_encode([
                      "session" => [
                            "name" => "Session",
                            "minutes" => 1,
                            "seconds" => 0,
                            "color" =>"red"
                      ],
                        "break" => [
                            "name" => "Break",
                            "minutes" => 5,
                            "seconds" => 0,
                            "color" => "blue"
                        ],
                        "longBreak" => [
                            "name" => "Long Break",
                            "minutes" => 15,
                            "seconds" => 0,
                            "color" => "green"
                        ]
                ])
            ]
        ];

        foreach ($settings as $setting) {
            Setting::create(array_merge($setting, [
                'user_id' => $team->user_id,
                'team_id' => $team->id
            ]));
        }
    }
}
