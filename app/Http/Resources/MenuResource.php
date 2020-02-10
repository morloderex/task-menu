<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'field' => $this->resource->field,
            'max_depth' => $this->resource->max_depth,
            'max_children' => $this->resource->max_children
        ];
    }
}
