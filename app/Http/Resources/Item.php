<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Item extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $item = [
            'id' => $this->id,
            'title' => $this->title,
            'resource_type' => $this->resource_type,
            'done' => $this->done,
            'points' => $this->points,
            'commit_date' => $this->commit_date,
            'color' => $this->color,
            'stage' => $this->stage ? $this->stage->name : "",
            'duration' => $this->timeEntries->sum('duration'),
            'board_id' =>  $this->stage ? $this->stage->board_id : '',
            'fields' => $this->whenLoaded('fields', function () {
                return $this->fields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'name' => $field->name,
                        'value' => $field->value,
                        'type' => $field->type,
                        'options' => $field->options,
                    ];
                });
            }),
        ];

        foreach ($this->fields as $field) {
            $item[$field->field_name] = $field->value;
        }

        return $item;
    }
}
