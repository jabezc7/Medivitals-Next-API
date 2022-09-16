<?php

namespace App\Http\Resources\Group;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray($request): array
    {
        $return = [
            'id' => $this->id,
            'type_id' => $this->type_id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'ordering' => $this->ordering,
        ];

        if ($this->permissions) {
            $return['permissions'] = $this->permissions;
        }

        if ($this->sections) {
            $return['sections'] = $this->sections;
        }

        if ($this->type) {
            $return['type'] = $this->type;
        }

        return $return;
    }
}
