<?php

namespace App\Http\Resources\Device;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class DeviceCollection extends ResourceCollection
{
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($device) {
            return [
                'id' => $device->id,
                'imei' => $device->imei,
                'number' => $device->number,
                'nickname' => $device->nickname,
                'label' => $device->label,
                'value' => $device->value,
                'frequencies' => $device->frequencies
            ];
        });
    }
}
