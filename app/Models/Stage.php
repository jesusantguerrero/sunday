<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;
    protected $with = ['fields', 'items'];
    public function fields() {
        return $this->hasMany('App\Models\Field', 'stage_id', 'id');
    }
    public function items() {
        return $this->hasMany('App\Models\Item', 'stage_id', 'id');
    }
}
