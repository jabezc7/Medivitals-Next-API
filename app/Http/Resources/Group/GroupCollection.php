<?php

namespace App\Http\Resources\Group;

use App\Http\Resources\Permission\PermissionCollection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class GroupCollection extends ResourceCollection
{
    public function toArray($request): array|Collection|\JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($group) {
            $return = [
                'id' => $group->id,
                'slug' => $group->slug,
                'name' => $group->name,
                'description' => $group->description,
                'active' => $group->active,
                'type' => new GroupTypeResource($group->type),
            ];

            if ($group->permissions) {
                $return['permissions'] = new PermissionCollection($group->permissions);
            }

            return $return;
        });
    }
}
