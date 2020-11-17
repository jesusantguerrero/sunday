<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;
    protected $with = ['options','rules'];
    protected $fillable = ['name','board_id', 'title', 'type', 'options', 'user_id', 'team_id', "manual"];

    public function options() {
        return $this->hasMany('App\Models\Label', 'field_id', 'id');
    }

    public function rules() {
        return $this->hasMany('App\Models\FieldRule', 'field_id', 'id');
    }

    public function fieldValues() {
        return $this->hasMany('App\Models\FieldValue', 'field_id', 'id');
    }

    public function labels() {
        return $this->hasMany('App\Models\Label', 'field_id', 'id');
    }

    public function deleteRelated() {
        $this->rules()->delete();
        $this->options()->delete();
        $this->labels()->delete();
        $this->fieldValues()->delete();
    }
}
