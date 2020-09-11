<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    public function stages() {
        return $this->hasMany('App\Models\Stage', 'board_id', 'id');
    }

    public function createMainStage() {
        $stage = $this->stages()->create([
            'name' => $this->name
        ]);

        $fields = [
            [
                "name" => 'owner',
                "title" => "Owner",
                "type" => "person"
            ],
            [
                "name" => 'status',
                "title" => "Status",
                "type" => "label"
            ],
            [
                "name" => 'due_date',
                "title" => "Due Date",
                "type" => "date"
            ],
            [
                "name" => 'priority',
                "title" => "Priority",
                "type" => "label"
            ],
        ];

        foreach ($fields as $field) {
            $stage->fields()->create([
                'name' => $field['name'],
                'title' => $field['title'],
                'type' => $field['type'],
            ]);
        }
    }

    public function deleteStages() {
        $stages = $this->stages();

        foreach ($stages as $stage) {
            $stage->deleteRelated();
        }
        $stages->delete();
    }
}
