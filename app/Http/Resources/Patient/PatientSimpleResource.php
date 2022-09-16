<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientSimpleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'full_name' => $this->first.' '.$this->last
        ];
    }
}
