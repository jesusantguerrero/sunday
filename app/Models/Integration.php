<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use HasFactory;
    protected $fillable = ["user_id", "team_id", "automation_service_id", "name", "token", "hash"];
    public function automations()
    {
        return $this->hasMany('App\Models\Automation', 'integration_id', 'id');
    }
}
