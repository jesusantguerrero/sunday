<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationRecipe extends Model
{
    use HasFactory;
    public function service() {
        return $this->belongsTo('App\Models\AutomationService', 'automation_service_id', 'id');
    }
}
