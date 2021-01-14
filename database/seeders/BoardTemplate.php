<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoardTemplate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            // Board
            DB::table('board_types')->insert([
                'name' => 'board',
                'title' => 'Board'
            ]);

            // Notebook
            DB::table('board_types')->insert([
                'name' => 'notebook',
                'title' => 'Notebook'
            ]);

            // Notebook
            DB::table('board_types')->insert([
                'name' => "okr",
                'title' => "OKR's"
            ]);

            // Board Types
            // Heisenhower
            DB::table('board_templates')->insert([
                'board_type_id' => 1,
                'name' => 'heisenhower',
                'title' => 'Heisenhower Matrix',
                'config' => json_encode([
                    "fields" => [
                        [
                            "name" => 'owner',
                            "title" => "Owner",
                            "type" => "person"
                        ],
                        [
                            "name" => 'status',
                            "title" => "Status",
                            "type" => "label",
                            "labels" => [
                                [
                                    "name" => "backlog",
                                    "label" => "Backlog",
                                    "color" => "white"
                                ],
                                [
                                    "name" => "todo",
                                    "label" => "Todo",
                                    "color" => "green"
                                ],
                                [
                                    "name" => "schedule",
                                    "label" => "Schedule",
                                    "color" => "blue"
                                ],
                                [
                                    "name" => "delegate",
                                    "label" => "Delegate",
                                    "color" => "yellow"
                                ],
                                [
                                    "name" => "delete",
                                    "label" => "Delete",
                                    "color" => "red"
                                ]
                            ]
                        ],
                        [
                            "name" => 'due_date',
                            "title" => "Due Date",
                            "type" => "date"
                        ],
                        [
                            "name" => 'priority',
                            "title" => "Priority",
                            "type" => "label",
                            "labels" => [
                                [
                                    "name" => "low",
                                    "label" => "Low",
                                    "color" => "green"
                                ],
                                [
                                    "name" => "medium",
                                    "label" => "Medium",
                                    "color" => "yellow"
                                ],
                                [
                                    "name" => "high",
                                    "label" => "High",
                                    "color" => "red"
                                ]
                            ]
                        ]
                    ],
                    "max_todo_task" => 8
                ])
            ]);

            // Agile
            DB::table('board_templates')->insert([
                'board_type_id' => 1,
                'name' => 'agile',
                'title' => 'agile',
                'config' => json_encode([
                    "fields" => [
                        [
                            "name" => 'owner',
                            "title" => "Owner",
                            "type" => "person"
                        ],
                        [
                            "name" => 'status',
                            "title" => "Status",
                            "type" => "label",
                            "labels" => [
                                [
                                    "name" => "progress",
                                    "label" => "In Progress",
                                    "color" => "blue"
                                ],
                                [
                                    "name" => "todo",
                                    "label" => "Todo",
                                    "color" => "white"
                                ],
                                [
                                    "name" => "review",
                                    "label" => "Peer Review",
                                    "color" => "yellow"
                                ],
                                [
                                    "name" => "qa",
                                    "label" => "QA",
                                    "color" => "green"
                                ],
                                [
                                    "name" => "done",
                                    "label" => "Done",
                                    "color" => "green"
                                ]
                            ]
                        ],
                        [
                            "name" => 'due_date',
                            "title" => "Due Date",
                            "type" => "date"
                        ],
                        [
                            "name" => 'priority',
                            "title" => "Priority",
                            "type" => "label",
                            "labels" => [
                                [
                                    "name" => "low",
                                    "label" => "Low",
                                    "color" => "green"
                                ],
                                [
                                    "name" => "medium",
                                    "label" => "Medium",
                                    "color" => "yellow"
                                ],
                                [
                                    "name" => "high",
                                    "label" => "High",
                                    "color" => "red"
                                ]
                            ]
                        ]
                    ],
                    'max_in_progress_tasks' => 3
                ])
            ]);

            // habitical
            DB::table('board_templates')->insert([
                'board_type_id' => 1,
                'name' => 'habitica',
                'title' => 'Habitica',
                'config' => json_encode([
                    "fields" => [
                        [
                            "name" => 'owner',
                            "title" => "Owner",
                            "type" => "person"
                        ],
                        [
                            "name" => 'status',
                            "title" => "Status",
                            "type" => "label",
                            "labels" => [
                                [
                                    "name" => "backlog",
                                    "label" => "Backlog",
                                    "color" => "white"
                                ],
                                [
                                    "name" => "todo",
                                    "label" => "Todo",
                                    "color" => "green"
                                ],
                                [
                                    "name" => "schedule",
                                    "label" => "Schedule",
                                    "color" => "blue"
                                ],
                                [
                                    "name" => "delegate",
                                    "label" => "Delegate",
                                    "color" => "yellow"
                                ],
                                [
                                    "name" => "delete",
                                    "label" => "Delete",
                                    "color" => "red"
                                ]
                            ]
                        ],
                        [
                            "name" => 'due_date',
                            "title" => "Due Date",
                            "type" => "date"
                        ],
                        [
                            "name" => 'priority',
                            "title" => "Priority",
                            "type" => "label",
                            "labels" => [
                                [
                                    "name" => "low",
                                    "label" => "Low",
                                    "color" => "green"
                                ],
                                [
                                    "name" => "medium",
                                    "label" => "Medium",
                                    "color" => "yellow"
                                ],
                                [
                                    "name" => "high",
                                    "label" => "High",
                                    "color" => "red"
                                ]
                            ]
                        ]
                    ],
                    "stages" => ["Habits", "Dailies", "To Do's"]
                ])
            ]);
    }
}
