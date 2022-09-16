<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Device extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'imei',
        'number',
        'nickname'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'frequencies' => 'array',
    ];

    protected $append = ['label', 'value'];

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'device_patient', 'device_id', 'patient_id');
    }

    public function getLabelAttribute() {
        return $this->nickname.' ('.$this->imei.')';
    }

    public function getValueAttribute() {
        return $this->id;
    }
}
