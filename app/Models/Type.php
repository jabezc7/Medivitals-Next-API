<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Type extends BaseModel
{
    use HasSlug, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'group',
        'container',
        'abbreviation',
        'value',
        'active',
        'meta',
        'ordering',
        'locked',
        'description',
    ];

    protected $appends = [
        'group_name',
        'colour'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'value' => 'integer',
        'ordering' => 'integer',
        'active' => 'boolean',
        'locked' => 'boolean',
        'meta' => 'json',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['group', 'name'])
            ->saveSlugsTo('slug');
    }

    public function getGroupNameAttribute(): string
    {
        if ($this->group) {
            return ucwords(str_replace('-', ' ', $this->group));
        } else {
            return '';
        }
    }

    public function getOrderingAttribute($value): string
    {
        if ($value == 0) {
            return '';
        } else {
            return $value;
        }
    }

    public function getColourAttribute(){
        if ($this->meta && isset($this->meta['colour'])){
            return $this->meta['colour'];
        } else {
            return '';
        }
    }

    public static function lookup($group, $fields = ['id', 'name', 'slug', 'group'], $orderColumn = 'name', $orderDirection = 'ASC') : Collection
    {
        return self::where('group', $group)->select($fields)->orderBy($orderColumn, $orderDirection)->get();
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

    public static function getTypeNameByID($statusID): string
    {
        return self::query()->select(['name'])->where('id', $statusID)->first()->name;
    }

    public static function getTypeIDBySlug($slug): string
    {
        return self::query()->select(['id'])->where('slug', $slug)->first()->id;
    }
}
