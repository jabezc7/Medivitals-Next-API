<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends BaseModel
{
    use LogsActivity;

    public $timestamps = false;

    protected $fillable = [
        'key',
        'value'
    ];

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
