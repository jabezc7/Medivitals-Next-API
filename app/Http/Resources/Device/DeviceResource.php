<?php

namespace App\Http\Resources\Device;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Patient\PatientCollection;

class DeviceResource extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'id' => $this->id,
            'imei' => $this->imei,
            'number' => $this->number,
            'nickname' => $this->nickname,
            'patients' => new PatientCollection($this->patients),
            'meta' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
