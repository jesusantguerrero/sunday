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
            'done' => $this->done,
            'commit_date' => $this->commit_date,
            'stage' => $this->stage ? $this->stage->name : "",
            'duration' => $this->timeEntries->sum('duration'),
            'board_id' =>  $this->stage ? $this->stage->board_id : '',
        ];

        foreach ($this->fields as $field) {
            $item[$field->field_name] = $field->value;
        }

        return $item;
    }
}
