<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\UserTrait;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\Sanctum;
use Spatie\Sluggable\HasSlug;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Uuid, SoftDeletes, HasSlug, UserTrait, HasFactory, LogsActivity;

    protected static function booted()
    {
        static::addGlobalScope(new UserScope);
    }

    protected $fillable = [
        'first',
        'last',
        'email',
        'mobile',
        'phone',
        'position',
        'password',
        'super_admin',
        'login_count',
        'last_login',
        'active',
        'company_name',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'super_admin' => 'boolean',
        'login_count' => 'integer',
        'avatar' => 'array'
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function tokens(): MorphMany
    {
        return $this->morphMany(Sanctum::$personalAccessTokenModel, 'tokenable', 'tokenable_type', 'tokenable_uuid');
    }

    public function isSuperAdmin(): bool
    {
        return $this->groups()->where('slug', 'super-administrator')->exists();
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'assignee_id', 'id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'created_by');
    }

    public function sms(): MorphToMany
    {
        return $this->morphToMany(Sms::class, 'smsable')->orderBy('updated_at', 'DESC');
    }

    public static function data($filterClass, $request, $type = null, $select = null, $sort_column = 'last', $sort_direction = 'ASC'): Collection|LengthAwarePaginator|array
    {
        $sortColumn = $request->get('sortColumn') ? $request->get('sortColumn') : $sort_column;
        $sortDir = $request->get('sortDir') ? $request->get('sortDir') : $sort_direction;
        $filters = json_decode($request->get('filters'));

        $query = self::when($select, function ($query) use ($select) {
            $query->select($select);
        })->when($sortColumn && $sortDir, function ($query) use ($request, $sortColumn, $sortDir) {
            $query->orderBy($sortColumn, $sortDir);
        })->when(isset($filters->type), function ($query) use ($filters) {
            if ($filters->type === 'system') {
                if ($customerGroup = Group::where('slug', 'customer')->first()) {
                    $query->whereHas('groups', function ($q) use ($customerGroup) {
                        $q->where('group_id', '<>', $customerGroup->id);
                    });
                }
            } elseif ($filters->type === 'customers' || $filters->type === 'customer') {
                if ($customerGroup = Group::where('slug', 'customer')->first()) {
                    $query->whereHas('groups', function ($q) use ($customerGroup) {
                        $q->where('group_id', $customerGroup->id);
                    });
                }
            }
        })->when(isset($filters->group), function ($query) use ($filters) {
            if (is_string($filters->group) && $filters->group != '') {
                $query->whereHas('groups', function ($q) use ($filters) {
                    $q->where('group_id', $filters->group);
                });
            } elseif (is_object($filters->group) && isset($filters->group->value) && $filters->group->value != '') {
                $query->whereHas('groups', function ($q) use ($filters) {
                    $q->where('group_id', $filters->group->value);
                });
            }
        })->where(function ($query) use ($filters, $request) {
            // Search
            if (isset($filters->search) && $filters->search->value != '') {
                $query->where(function ($q) use ($filters) {
                    $q->where('first', 'like', '%'.$filters->search->value.'%')
                        ->orWhere('last', 'like', '%'.$filters->search->value.'%')
                        ->orWhere('email', 'like', '%'.$filters->search->value.'%')
                        ->orWhere('mobile', 'like', '%'.$filters->search->value.'%')
                        ->orWhere('phone', 'like', '%'.$filters->search->value.'%');
                });
            }

            // Active
            if ($request->get('active')) {
                $query->where('active', $request->get('active'));
            }
        })->when($request->get('with'), function ($query) use ($request) {
            $withs = explode(',', $request->get('with'));

            foreach ($withs as $with) {
                $query->with($with);
            }
        });

        if ($type == 'datatable') {
            return $query->paginate($request->get('limit') ? $request->get('limit') : 25);
        } else {
            if ($request->get('limit')) {
                $query->limit($request->get('limit'));
            }

            return $query->get();
        }
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
