<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldValue extends Model
{
    use HasFactory;
    protected $fillable = ['resource', 'field_id', 'field_name', 'entity_id', 'value', 'user_id', 'team_id'];
}
