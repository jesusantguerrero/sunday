<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    public function stages() {
        return $this->hasMany('App\Models\Stage', 'board_id', 'id')->orderBy('order');
    }

    public function createMainStage() {
        $stage = $this->stages()->create([
            'user_id' => $this->user_id,
            'team_id' => $this->team_id,
            'name' => $this->name
        ]);

        $stage->setUp();
    }

    public function deleteStages() {
        $stages = $this->stages();

        foreach ($stages as $stage) {
            $stage->deleteRelated();
        }
        $stages->delete();
    }
}
