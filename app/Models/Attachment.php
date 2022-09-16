<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'path',
        'meta',
        'group',
        'mime',
        'folder',
        'created_by',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
