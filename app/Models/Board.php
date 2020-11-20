<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;
    protected $with = ['labels', 'fields'];

    public function stages() {
        return $this->hasMany('App\Models\Stage', 'board_id', 'id')->orderBy('order');
    }

    public function fields() {
        return $this->hasMany('App\Models\Field', 'board_id', 'id');
    }

    public function labels() {
        return $this->hasMany('App\Models\Label', 'board_id', 'id');
    }

    public function createMainStage() {
        $this->stages()->create([
            'user_id' => $this->user_id,
            'team_id' => $this->team_id,
            'name' => $this->name
        ]);

        $this->setUp();
    }

    public function setUp() {
        $fields = [
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
            ],
        ];

        foreach ($fields as $field) {
            $fieldDB = $this->fields()->create([
                'user_id' => $this->user_id,
                'team_id' => $this->team_id,
                'name' => $field['name'],
                'title' => $field['title'],
                'type' => $field['type'],
            ]);

            if (isset($field['labels'])) {
                foreach ($field['labels'] as $label) {
                    $fieldDB->labels()->create([
                        'user_id' => $fieldDB->user_id,
                        'team_id' => $fieldDB->team_id,
                        'board_id' => $fieldDB->board_id,
                        'name' => $label['name'],
                        'label' => $label['label'],
                        'color' => $label['color'],
                    ]);
                }

                $fieldDB->rules()->create([
                    'user_id' => $fieldDB->user_id,
                    'team_id' => $fieldDB->team_id,
                    'board_id' => $fieldDB->board_id,
                    'name'=> 'bg',
                    'reference' => 'options'
                ]);
            }
        }
    }

    public function findOrCreateField($fieldData) {
        $fieldToSearch = isset($fieldData['name']) ? 'name' : 'id';
        $valueToSearch = $fieldData['name'] ?? $fieldData['field_id'] ?? "";
        $field = $this->fields()->where(["$fieldToSearch" =>  "$valueToSearch"])->limit(1)->get();
        $field = count($field) ? $field[0] : null;
        if (!$field) {
            $field = $this->fields()->create([
                'user_id' => $this->user_id,
                'team_id' => $this->team_id,
                'name' => $fieldData['name'],
                'title' => empty($fieldData['title']) ? \ucwords($fieldData['name']) : $fieldData['title'],
                'type' => $fieldData['type'] ?? 'text',
                'hide' => $fieldData['hide'] ?? 0,
            ]);
        }
        return $field;
    }

    public function deleteStages() {
        $stages = $this->stages();

        foreach ($stages as $stage) {
            $stage->deleteRelated();
        }
        $stages->delete();
    }
}
