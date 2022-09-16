<?php

namespace App\Http\Resources\Section;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray($request): array
    {
        $return = [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'route' => $this->route,
            'ordering' => $this->ordering,
            'level' => $this->level,
            'active' => $this->active,
            'hidden' => $this->hidden,
        ];

        if ($this->parent) {
            $return['parent'] = new SectionRelationshipResource($this->parent);
        }

        return $return;
    }
}
