<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSimpleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'first' => $this->first,
            'last' => strtoupper($this->last),
            'full_name' => $this->first.' '.$this->last,
            'full_name_reversed' => strtoupper($this->last).', '.$this->first,
            'position' => $this->position,
            'mobile' => $this->mobile
        ];
    }
}
