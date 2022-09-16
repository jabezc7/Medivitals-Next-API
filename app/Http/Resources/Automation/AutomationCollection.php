<?php

namespace App\Http\Resources\Automation;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class AutomationCollection extends ResourceCollection
{
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($automation) {
            return [
                'id' => $automation->id,
                'patient_id' => $automation->patient_id,
                'patient' => $automation->patient ? $automation->patient->first . ' ' . $automation->patient->last : '',
                'name' => $automation->name,
                'active' => $automation->active,
                'global' => $automation->global,
                'triggers' => $automation->triggers,
                'actions' => new ActionCollection($automation->actions)
            ];
        });
    }
}
