<?php

namespace App\Models;

class DevicePatient extends BaseModel
{
    protected $table = 'device_patient';

    public $timestamps = false;

    protected $fillable = [
        'device_id',
        'patient_id'
    ];
}
