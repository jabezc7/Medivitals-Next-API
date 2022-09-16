<?php

namespace App\Http\Resources\Patient;

use App\Http\Resources\Device\DeviceCollection;
use App\Http\Resources\Notification\NotificationCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use App\Http\Resources\Note\NoteCollection;
use App\Http\Resources\User\UserSimpleResource;

class PatientResource extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'first' => $this->first,
            'last' => $this->last,
            'full_name' => $this->first.' '.$this->last,
            'full_name_reversed' => $this->last.', '.$this->first,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'active' => $this->active,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'suburb' => $this->suburb,
            'postcode' => $this->postcode,
            'state' => $this->state,
            'country' => $this->country,
            'medicare_number' => $this->medicare_number,
            'medicare_expiry' => $this->medicare_expiry,
            'medicare_position' => $this->medicare_position,
            'private_health_fund' => $this->private_health_fund,
            'private_health_membership_no' => $this->private_health_membership_no,
            'gp_medical_centre' => $this->gp_medical_centre,
            'gp_name' => $this->gp_name,
            'gp_phone' => $this->gp_phone,
            'gp_email' => $this->gp_email,
            'assignee_id' => $this->assignee_id,
            'notes' => new NoteCollection($this->notes),
            'notifications' => new NotificationCollection($this->notifications()->orderBy('created_at', 'DESC')->get()),
            'devices' => new DeviceCollection($this->devices),
            'assignee' => new UserSimpleResource($this->assignee),
            'stats' => [

                    $this->patientData()
                    ->where('type', 'blood_pressure')
                    ->limit(1)
                    ->orderby('created_at', 'desc')
                    ->get()
                    ->map(function ($data) {
                        return [
                            'id' => $data->id,
                            'title' => 'Blood Pressure',
                            'value' => (int) Str::before($data->value, '/') . '/' . (int) Str::after($data->value, '/'),
                            'unit' => 'mmHg',
                            'last_reading' => $data->created_at,
                            'items' => [
                                [
                                    'name' => 'Systolic Blood Pressure',
                                    'value' => (int) Str::before($data->value, '/'),
                                    'unit' => 'mmHg'
                                ],
                                [
                                    'name' => 'Dyastolic Blood Pressure',
                                    'value' => (int) Str::after($data->value, '/'),
                                    'unit' => 'mmHg'
                                ]
                            ]
                        ];
                    })->first(),

                    $this->patientData()
                    ->where('type', 'heart_rate')
                    ->limit(1)
                    ->orderby('created_at', 'desc')
                    ->get()
                    ->map(function ($data) {
                        return [
                            'id' => $data->id,
                            'title' => 'Heart Rate',
                            'value' => (float) $data->value,
                            'unit' => 'bpm',
                            'last_reading' => $data->created_at,
                            'items' => [
                                [
                                    'name' => 'Heart Rate',
                                    'value' => (float) $data->value,
                                    'unit' => 'bpm'
                                ]
                            ]
                        ];
                    })->first(),

                    $this->patientData()
                    ->where('type', 'temperature')
                    ->limit(1)
                    ->orderby('created_at', 'desc')
                    ->get()
                    ->map(function ($data) {
                        return [
                            'id' => $data->id,
                            'title' => 'Temperature',
                            'value' => (float) $data->value,
                            'unit' => '°c',
                            'last_reading' => $data->created_at,
                            'items' => [
                                [
                                    'name' => 'Temperature',
                                    'value' => (float) $data->value,
                                    'unit' => '°c'
                                ]
                            ]
                        ];
                    })->first(),

                    $this->patientData()
                    ->where('type', 'oxygen_saturation')
                    ->limit(1)
                    ->orderby('created_at', 'desc')
                    ->get()
                    ->map(function ($data) {
                        return [
                            'id' => $data->id,
                            'title' => 'O2 Saturation',
                            'value' => (float) $data->value,
                            'unit' => '%',
                            'last_reading' => $data->created_at,
                            'items' => [
                                [
                                    'name' => 'O2 Saturation',
                                    'value' => (float) $data->value,
                                    'unit' => '%'
                                ]
                            ]
                        ];
                    })->first(),

                    $this->patientData()
                    ->where('type', 'heart_rate')
                    ->limit(1)
                    ->orderby('created_at', 'desc')
                    ->get()
                    ->map(function ($data) {
                        return [
                            'id' => $data->id,
                            'title' => 'Respiratory Rate',
                            'value' => (int) ceil($data->value / 4),
                            'unit' => 'bpm',
                            'last_reading' => $data->created_at,
                            'items' => [
                                [
                                    'name' => 'Respiratory Rate',
                                    'value' => (int) ceil($data->value / 4),
                                    'unit' => 'bpm'
                                ]
                            ]
                        ];

                    })->first()
            ],
            'meta' => [
                'last_login' => $this->last_login,
                'login_count' => $this->login_count,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
