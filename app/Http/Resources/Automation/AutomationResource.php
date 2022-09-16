<?php

namespace App\Http\Resources\Automation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutomationResource extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'name' => $this->name,
            'description' => $this->description,
            'triggers' => $this->triggers,
            'actions' => $this->actions,
            'active' => $this->active,
            'meta' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
