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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'done' => $this->done,
            'commit_date' => $this->commit_date,
            'stage' => $this->stage->name,
            'duration' => $this->timeEntries->sum('duration')
        ];
    }
}
