<?php

namespace App\Http\Resources\Type;

use Illuminate\Http\Resources\Json\JsonResource;

class TypeResource extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'group' => $this->group,
            'container' => $this->container,
            'description' => $this->description,
            'abbreviation' => $this->abbreviation,
            'value' => $this->value,
            'active' => $this->active,
            'locked' => $this->locked,
            'default' => $this->default,
            'meta' => $this->meta,
            'colour' => $this->colour,
            'ordering' => $this->ordering
        ];
    }
}
