<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['title', 'user_id', 'team_id'];
    protected $with = ['fields'];
    use HasFactory;


    public function fields() {
        return $this->hasMany('App\Models\FieldValue', 'entity_id', 'id');
    }

    public function saveFields($fields) {
        foreach ($fields as $field) {

            $fieldInstance = FieldValue::where(['field_id' => $field['field_id'], 'entity_id' => $this->id])->get();
            if (count($fieldInstance) && isset($fieldInstance[0])) {
                $fieldInstance[0]->value = isset($field['value']) ? $field['value'] : '';
                $fieldInstance[0]->save();
            } else {
                $this->fields()->create([
                    'user_id' => $this->user_id,
                    'team_id' => $this->team_id,
                    'resource' => 'item',
                    'field_id' => $field['field_id'],
                    'field_name' => $field['field_name'],
                    'value' => $field['value'] ?? ''
                ]);
            }
        }
    }

    public static function getByCustomField($fieldName, $value) {
        return Item::with(['fields' => function($query) use ($value){
            $query->where('value', $value);
        }])->get()->filter(function ($item) {
            return count($item->fields);
        });
    }
}
