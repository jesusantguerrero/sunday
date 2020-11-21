<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Automation extends Model
{
    use HasFactory;

    public function recipe() {
        return $this->belongsTo('App\Models\AutomationRecipe', 'automation_recipe_id', 'id');
    }
}
