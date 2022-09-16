<?php

namespace App\Http\Resources\Group;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class GroupRelationshipCollection extends ResourceCollection
{
    public function toArray($request): array|Collection|\JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($group) {
            return [
                'id' => $group->id,
                'slug' => $group->slug,
                'name' => $group->name,
            ];
        });
    }
}
