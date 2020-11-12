<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;
    protected $with = ['items'];
    protected $fillable = ['name','order', 'color', 'board_id', 'user_id', 'team_id'];

    public function items() {
        return $this->hasMany('App\Models\Item', 'stage_id', 'id')->orderBy('order');
    }

    public function deleteRelated() {
        $fields = $this->fields();
        foreach ($fields as $field) {
            $field->deleteRelated();
            $field->delete();
        }

        $this->items()->delete();
    }
}
