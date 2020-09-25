<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;
    protected $with = ['fields', 'items'];
    protected $fillable = ['name', 'board_id', 'user_id', 'team_id'];

    public function fields() {
        return $this->hasMany('App\Models\Field', 'stage_id', 'id');
    }

    public function items() {
        return $this->hasMany('App\Models\Item', 'stage_id', 'id');
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
                        'stage_id' => $fieldDB->stage_id,
                        'name' => $label['name'],
                        'label' => $label['label'],
                        'color' => $label['color'],
                    ]);
                }

                $fieldDB->rules()->create([
                    'user_id' => $fieldDB->user_id,
                    'team_id' => $fieldDB->team_id,
                    'stage_id' => $fieldDB->stage_id,
                    'name'=> 'bg',
                    'reference' => 'options'
                ]);
            }
        }
    }

    public function deleteRelated() {
        $fields = $this->fields();
        foreach ($fields as $field) {
            $field->deleteRelated();
            $field->delete();
        }

        $this->items()->delete();
    }
}
