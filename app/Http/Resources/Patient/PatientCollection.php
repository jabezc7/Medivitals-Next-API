<?php

namespace App\Http\Resources\Patient;

use App\Http\Resources\User\UserSimpleResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class PatientCollection extends ResourceCollection
{
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($patient) {
            return [
                'id' => $patient->id,
                'slug' => $patient->slug,
                'first' => $patient->first,
                'last' => $patient->last,
                'full_name' => $patient->first.' '.$patient->last,
                'full_name_reversed' => $patient->last.', '.$patient->first,
                'email' => $patient->email,
                'mobile' => $patient->mobile,
                'active' => $patient->active,
                'assignee' => optional($patient->assignee)->only(['id', 'first', 'last']),
                'last_viewed_at' => $patient->last_viewed_at ?? null,
                'devices' => $patient->devices
            ];
        });
    }
}
