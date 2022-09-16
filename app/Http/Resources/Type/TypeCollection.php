<?php

namespace App\Http\Resources\Type;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class TypeCollection extends ResourceCollection
{
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'slug' => $type->slug,
                'group' => $type->group,
                'group_name' => $type->group_name,
                'value' => $type->value,
                'active' => $type->active,
                'locked' => $type->locked,
                'ordering' => $type->ordering,
                'colour' => $type->colour
            ];
        });
    }
}
