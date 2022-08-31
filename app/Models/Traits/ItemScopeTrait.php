<?php

namespace App\Models\Traits;

use App\Models\FieldValue;
use DateTime;

trait ItemScopeTrait {
    public function scopeByCustomField($query, $entry) {
        return $query->whereHas('fields', function($query) use ($entry){
            $query->where('value', $entry[1]);
            if (DateTime::createFromFormat('Y-m-d', $entry[0])) {
                $query->orWhere('date_value', $entry[1]);
            }
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $done = $filters['done'] = $filters['done'] ?? -1;

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%'.$search.'%');
        })->when($done , function ($query, $done) {
            if ($done == 'only') {
                $query->where('done', 1);
            } elseif ($done == -1) {
                $query->whereNull('commit_date');
            }
        });
    }

    public function scopeOrderByField($query, $sortFilter)
    {
        $sort = $sortFilter['sort'] ?? 'order';
        $direction =  strpos($sort, "-") === 0 ? "DESC" : "ASC";
        $sort = $direction == "ASC" ? $sort : substr($sort, 1);

        if ($sort == 'title' || $sort == 'order') {
            return $query->orderBy($sort, $direction);
        } else {
            return $query->orderBy(FieldValue::select('value')
                ->whereColumn('field_values.entity_id', 'items.id')
                ->where('field_values.field_name', $sort)
                ->take(1),
                $direction
            );
        }
    }
}
