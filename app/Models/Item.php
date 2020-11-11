<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Checklist;

class Item extends Model
{
    protected $fillable = ['board_id', 'team_id', 'stage_id', 'title', 'order', 'user_id', 'done','commit_date'];
    protected $with = ['fields', 'checklist'];
    use HasFactory;


    public function fields() {
        return $this->hasMany('App\Models\FieldValue', 'entity_id', 'id');
    }

    public function checklist() {
        return $this->hasMany('App\Models\Checklist', 'item_id', 'id')->orderBy('order');
    }

    public function stage() {
        return $this->belongsTo('App\Models\Stage', 'stage_id', 'id');
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

    public function saveChecklist($list) {
        Checklist::where(['item_id' => $this->id])->delete();

        foreach ($list as $check) {
            $this->checklist()->create([
                'user_id' => $this->user_id,
                'team_id' => $this->team_id,
                'item_id' => $this->id,
                'title' => $check['title'],
                'done' => $check['done'] ?? false,
                'order' => $check['order'] ?? 0
            ]);
        }
    }

    public function timeEntries() {
        return $this->hasMany('App\Models\TimeEntry', 'item_id', 'id');
    }

    public static function getByCustomField($entry, $user) {
        return Item::whereHas('fields', function($query) use ($entry){
            $query->where('value', $entry[1]);
        })->with('stage')->where([
            'team_id' => $user->current_team_id,
            'user_id' => $user->id,

        ])->whereNull('commit_date')->get();
    }

    public function scopeFilter($query, array $filters)
    {
        $filters['done'] = $filters['done'] ?? -1;

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%'.$search.'%');
        })->when($filters['done'] ?? -1 , function ($query, $done) {
            if ($done == 'only') {
                $query->where('done', 1);
            } elseif ($done == -1) {
                $query->whereNull('commit_date');
            }
        });
    }
}
