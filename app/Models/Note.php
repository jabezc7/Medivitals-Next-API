<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'parent_id',
        'note',
        'noteable_id',
        'noteable_type',
        'created_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [];

    protected static function booted()
    {
        parent::boot();

        self::creating(function ($note) {
            if (!$note->created_by) {
                $note->created_by = auth()->user()->id;
            }
        });
    }

    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
