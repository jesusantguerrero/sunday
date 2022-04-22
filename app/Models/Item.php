<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Checklist;
use DateTime;
use RRule\RRule;

class Item extends Model
{
    protected $fillable = ['board_id', 'team_id', 'stage_id','resource_id', 'rrule' ,'resource_type', 'resource_origin', 'title', 'order', 'user_id', 'done','commit_date', 'points'];
    protected $with = ['fields', 'checklist'];
    use HasFactory;


    protected static function boot()
    {
        parent::boot();
        Item::updating(function ($model) {
            if (!$model->commit_date && $model->done) {
                $model->commit_date = now()->format('Y-m-d');
            } else if (!$model->done) {
                $model->commit_date = null;
            }
        });

        Item::creating(function ($model) {
            $rrule = new RRule([
                'FREQ' => 'weekly',
                'INTERVAL' => 1,
                'BYDAY' => ['MO','TH', 'SA'],
                'DTSTART' => now()->format('Y-m-d'),
                'UNTIL' => '3099-12-31'
            ]);

            $model->rrule = $rrule->rfcString();
        });
    }

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
        if ($fields) {
            foreach ($fields as $field) {
                $boardField = $this->stage->board->findOrCreateField($field);
                $fieldInstance = FieldValue::where(['field_id' => $boardField->id, 'entity_id' => $this->id])->get();
                if (count($fieldInstance) && isset($fieldInstance[0])) {
                    $fieldInstance[0]->value = isset($field['value']) ? $field['value'] : '';
                    $fieldInstance[0]->save();
                } else {
                    $this->fields()->create([
                        'user_id' => $this->user_id,
                        'team_id' => $this->team_id,
                        'resource' => 'item',
                        'field_id' => $boardField->id,
                        'field_name' => $boardField->name,
                        'value' => $field['value'] ?? '',
                        'hide' => $field['hide'] ?? 0
                    ]);
                }
            }
        }
    }



    public function saveChecklist($list) {
        if ($list) {
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

    }

    public function timeEntries() {
        return $this->hasMany('App\Models\TimeEntry', 'item_id', 'id');
    }

    public static function createEvent($eventData, $uniqueBy = null) {
        $isSaved = null;
        if ($uniqueBy) {
            $savedItem = Item::where($uniqueBy)->limit(1)->get();
            $isSaved = count($savedItem);
        }

        if(!$isSaved) {
            $item = Item::create($eventData);
            $item->saveFields($eventData['fields']);
        } else {
            $savedItem[0]->update($eventData);
            $savedItem[0]->saveFields($eventData['fields']);
        }
    }

    public static function getByCustomField($entry, $user) {
        return Item::whereHas('fields', function($query) use ($entry){
            $query->where('value', $entry[1]);
            if (DateTime::createFromFormat('Y-m-d', $entry[0])) {
                $query->orWhere('date_value', $entry[1]);
            }
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
