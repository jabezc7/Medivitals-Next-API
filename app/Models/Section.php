<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Section extends BaseModel
{
    use HasSlug;

    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'route',
        'ordering',
        'level',
        'active',
        'hidden',
    ];

    protected $appends = [
        'parent_name',
    ];

    protected $casts = [
        'active' => 'boolean',
        'hidden' => 'boolean',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function getParentNameAttribute()
    {
        if ($this->parent_id) {
            if ($parent = self::find($this->parent_id)) {
                return $parent->name;
            }
        }
        return null;
    }
}
