<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Template extends BaseModel
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'type_id',
        'name',
        'view',
        'path',
        'content',
        'active',
        'quick_link'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'quick_link' => 'boolean'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function getActivityLogReferenceAttribute(): string
    {
        return $this->name;
    }
}
