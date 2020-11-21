<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Automation extends JsonResource
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
            'name' => $this->name,
            'recipe' => $this->recipe,
            'service_logo' => $this->recipe ? $this->recipe->service->logo : "",
            'service_name' => $this->recipe ? $this->recipe->service->name : "",
        ];

        return $item;
    }
}
