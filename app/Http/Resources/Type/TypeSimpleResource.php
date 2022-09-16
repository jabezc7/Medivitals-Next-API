<?php

namespace App\Http\Resources\Type;

use Illuminate\Http\Resources\Json\JsonResource;

class TypeSimpleResource extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug
        ];
    }
}
