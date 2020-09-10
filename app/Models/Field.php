<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;
    protected $with = ['options','rules'];
    public function options() {
        return $this->hasMany('App\Models\Label', 'field_id', 'id');
    }

    public function rules() {
        return $this->hasMany('App\Models\FieldRule', 'field_id', 'id');
    }
}
