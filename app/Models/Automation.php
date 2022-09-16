<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Automation extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'name',
        'description',
        'triggers',
        'actions',
        'active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'triggers' => 'array',
        'actions' => 'array',
        'active' => 'boolean',
        'global' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
