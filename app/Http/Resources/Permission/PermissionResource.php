<?php

namespace App\Http\Resources\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type_id' => $this->type_id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'route' => $this->route,
            'active' => $this->active,
            'hidden' => $this->hidden,
            'type' => new PermissionTypeResource($this->type),
        ];
    }
}
