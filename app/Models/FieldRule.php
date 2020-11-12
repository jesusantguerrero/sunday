<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldRule extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'team_id', 'board_id', 'name', 'reference'];
}
