<?php

namespace App\Http\Resources\Permission;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class PermissionCollection extends ResourceCollection
{
    public function toArray($request): array|Collection|\JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($permission) {
            return [
                'id' => $permission->id,
                'slug' => $permission->slug,
                'name' => $permission->name,
                'description' => $permission->description,
                'route' => $permission->route,
                'active' => $permission->active,
                'hidden' => $permission->hidden,
                'type' => new PermissionTypeResource($permission->type),
            ];
        });
    }
}
