<?php

namespace App\Http\Resources\Section;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class SectionCollection extends ResourceCollection
{
    public function toArray($request): array|Collection|\JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($section) {
            return [
                'id' => $section->id,
                'name' => $section->name,
                'slug' => $section->slug,
                'icon' => $section->icon,
                'route' => $section->route,
                'ordering' => $section->ordering,
                'level' => $section->level,
                'active' => $section->active,
                'hidden' => $section->hidden,
            ];
        });
    }
}
