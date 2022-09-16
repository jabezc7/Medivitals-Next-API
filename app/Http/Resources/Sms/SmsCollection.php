<?php

namespace App\Http\Resources\Sms;

use App\Http\Resources\Patient\PatientSimpleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class SmsCollection extends ResourceCollection
{
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($sms) {
            return [
                'patient' => $sms->patientInfo->first .' '. $sms->patientInfo->first,
                'message' => $sms->message,
                'to' => $sms->to,
                'created_at' => $sms->created_at
            ];
        });
    }
}
