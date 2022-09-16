<?php

namespace App\Http\Resources\Data;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class DataCollection extends ResourceCollection
{
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($data) {
            return [
                'id' => $data->id,
                'device_id' => $data->device_id,
                'patient_id' => $data->patient_id,
                'device' => $data->device->imei. ' (' .$data->device->nickname. ')',
                'value' => $data->value,
                'type' => $data->type,
                'data_type' => ucwords(str_replace('_', ' ', $data->type)),
                'created_at' => $data->created_at
            ];
        });
    }
}
