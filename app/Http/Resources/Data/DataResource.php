<?php

namespace App\Http\Resources\Data;

use Illuminate\Http\Resources\Json\JsonResource;

class DataResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id
        ];
    }
}
