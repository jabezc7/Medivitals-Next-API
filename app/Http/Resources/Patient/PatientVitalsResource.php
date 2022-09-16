<?php

namespace App\Http\Resources\Patient;

use App\Http\Resources\Device\DeviceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Note\NoteCollection;

class PatientVitalsResource extends JsonResource
{
    public function toArray($request) : array
    {
        $return = [
            'id' => $this->id,
            'blood_pressure' => [],
            'heart_rate' => [],
            'temperature' => [],
            'oxygen_saturation' => [],
            'respiratory' => []
        ];

        if (in_array("Blood Pressure", $this->sections))
            $return['blood_pressure'] = $this->bloodPressure($this, $this->range);

         if (in_array("Heart Rate", $this->sections))
            $return['heart_rate'] = $this->heartRate($this, $this->range);

        if (in_array("Temperature", $this->sections))
            $return['temperature'] = $this->temperature($this, $this->range);

        if (in_array("O2 Saturation", $this->sections))
            $return['oxygen_saturation'] = $this->saturation($this, $this->range);

        if (in_array("Respiratory Rate", $this->sections))
            $return['respiratory'] = $this->respiratory($this, $this->range);


        return $return;
    }
}
