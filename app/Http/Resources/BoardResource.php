<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class BoardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'stages' => $this->stages()->without('items')->get(),
            'link' =>  URL::route('boards', $this),
            'color' => $this->color,
            'template'=> $this->boardTemplate,
            'type' => $this->boardType
        ];
    }
}
