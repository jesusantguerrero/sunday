<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Checklist;
use DateTime;
use RRule\RRule;

trait ItemScopeTrait
{
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
        $sort = $filters['sort'] ?? 'order';

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%'.$search.'%');
        })->when($done, function ($query, $done) {
            if ($done == 'only') {
                $query->where('done', 1);
            } elseif ($done == -1) {
                $query->whereNull('commit_date');
            }
        })->when($sort, function ($query, $sort) {
            $direction =  strpos($sort, "-") === 0 ? "DESC" : "ASC";
            $sortField = $direction == "ASC" ? $sort : substr($sort, 1);
            if ($sortField == 'title') {
                $query->orderBy($sortField, $direction);
            } else {
                $query->scopeSortCustomField($query, $sort, $direction);
            }
        });
    }

    public function scopeSortCustomField($query, $sort) {
        $direction =  strpos($sort, "-") === 0 ? "DESC" : "ASC";
        $sortField = $direction == "ASC" ? $sort : substr($sort, 1);
        
        $query->whereHas('fields', function ($query) use ($sortField) {
            $query->where('name', $sortField);
        });
    }
}
