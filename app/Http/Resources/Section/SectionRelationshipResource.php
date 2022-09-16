<?php

namespace App\Http\Resources\Section;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionRelationshipResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
