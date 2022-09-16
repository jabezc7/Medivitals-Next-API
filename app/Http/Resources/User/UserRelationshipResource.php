<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRelationshipResource extends JsonResource
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
            'email' => $this->email,
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'active' => $this->active,
            'position' => $this->position,
        ];
    }
}
