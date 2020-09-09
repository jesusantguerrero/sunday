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
}
