<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Scopes\PatientScope;
use App\Traits\UserTrait;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\Sanctum;
use Spatie\Sluggable\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Patient extends Authenticatable
{
    use HasFactory, UserTrait, HasSlug, SoftDeletes, Uuid, HasApiTokens, Notifiable;

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
        'avatar',
        'living_status',
        'address_1',
        'address_2',
        'suburb',
        'postcode',
        'state',
        'country',
        'medicare_number',
        'medicare_expiry',
        'medicare_position',
        'private_health_fund',
        'private_health_membership_no',
        'gp_medical_centre',
        'gp_name',
        'gp_phone',
        'gp_email',
        'assignee_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'super_admin' => 'boolean',
        'login_count' => 'integer',
        'avatar' => 'array',
        'medicare_expiry' => 'datetime',

    ];

    protected $keyType = 'string';

    protected $table = 'users';

    public $incrementing = false;

    protected static function booted()
    {
        static::addGlobalScope(new PatientScope);
        static::created(function ($patient) {
            $patientGroup = Group::query()->where('slug', 'patient')->first();
            $patient->groups()->attach($patientGroup);
        });
    }

    public function scopeFilter(Builder $builder, Filterable $filter, $filters)
    {
        $filter->apply($builder, $filters);
    }

    public function tokens(): MorphMany
    {
        return $this->morphMany(Sanctum::$personalAccessTokenModel, 'tokenable', 'tokenable_type', 'tokenable_uuid');
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'device_patient', 'patient_id', 'device_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user', 'user_id', 'permission_id');
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'section_user', 'user_id', 'section_id');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->orderBy('updated_at', 'DESC');
    }

    public function sms(): MorphToMany
    {
        return $this->morphToMany(Sms::class, 'smsable')->orderBy('updated_at', 'DESC');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function isSuperAdmin(): bool
    {
        return false;
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'patient_id', 'id');
    }

    public function automations(): HasMany
    {
        return $this->hasMany(Automation::class, 'patient_id', 'id');
    }

    public static function data($filterClass, $request, $type = null, $select = null, string $sort_column = 'name', string $sort_direction = 'ASC'): mixed
    {
        $sortColumn = $request->get('sortColumn') ? $request->get('sortColumn') : $sort_column;
        $sortDir = $request->get('sortDir') ? $request->get('sortDir') : $sort_direction;
        $filters = json_decode($request->get('filters'));
        $params = json_decode($request->get('query'));

        $query = self::when($select, function ($query) use ($select) {
            $query->select($select);
        })->when($sortColumn && $sortDir, function ($query) use ($request, $sortColumn, $sortDir) {
            $query->orderBy($sortColumn, $sortDir);
        })->where(function ($query) use ($filters, $request, $filterClass, $params) {
            // Search
            if ($filterClass) {
                $query->filter(new $filterClass, $filters);
            }

            // Active
            if (isset($filters->active)) {
                if (is_string($filters->active) && $filters->active != '') {
                    $query->where('active', $filters->active);
                } elseif (is_object($filters->active) && isset($filters->active->value) && $filters->active->value != '') {
                    $query->where('active', $filters->active->value);
                }
            } else {
                if ($request->get('active')) {
                    $query->where('active', $request->get('active'));
                }
            }

            // Query Params
            if ($params && count($params) > 0) {
                foreach ($params as $param) {
                    switch ($param->operator) {
                        case 'IS NULL':
                            $query->whereNull($param->field);
                        break;
                        case '=':
                            $query->where($param->field, $param->value);
                        break;
                        case '<>':
                            $query->where($param->field, '<>', $param->value);
                        break;
                    }
                }
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
            return $query->get();
        }
    }

    public function patientData(): HasMany
    {
        return $this->hasMany(Data::class);
    }

    public function vitalsData($query, $range, $type)
    {
        $filter = $type;

        if ($type == 'respiratory') {
            $filter = 'heart_rate';
        }

        return $query->patientData($query)
            ->where('type', $filter)
            ->when($range && $range > 0, function ($query) use ($range) {
                $query->where('created_at', '>=', now()->subHours($range));
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($data) use ($type, $filter) {
                $arr = [
                    'id' => $data->id,
                    'time' => $data->created_at,
                ];

                if ($filter == 'blood_pressure') {
                    $sys = (int)Str::before($data->value, '/');
                    $dyas = (int)Str::after($data->value, '/');

                    if (is_null($data->device_id)) {
                        $sys = ['y' => $sys, 'marker' => ['fillColor' => '#FFA500']];
                        $dyas = ['y' => $dyas, 'marker' => ['fillColor' => '#FFA500']];
                    }

                    $arr['systolic'] = $sys;
                    $arr['dyastolic'] = $dyas;
                } else if ($type == 'respiratory') {
                    $resp = (int)ceil($data->value / 4);

                    if (is_null($data->device_id)) {
                        $resp = ['y' => $resp, 'marker' => ['fillColor' => '#FFA500']];
                    }

                    $arr[$type] = $resp;
                } else {
                    $others = (float)$data->value;

                    if (is_null($data->device_id)) {
                        $others = ['y' => $others, 'marker' => ['fillColor' => '#FFA500']];
                    }

                    $arr[$type] = $others;
                }

                return $arr;
            })->toArray();
    }

    public function bloodPressure($query, $range): array
    {
        $data = $this->vitalsData($query, $range, 'blood_pressure');

        $count = count($data);
        $lastDate = 0;
        $lastSystolic = 0;
        $lastDyastolic = 0;

        if ($count > 0) {
            $lastDate = $data[count($data) - 1]['time'];
            $lastSystolic = $data[count($data) - 1]['systolic'];
            $lastDyastolic = $data[count($data) - 1]['dyastolic'];
        }

        $d = [120, 30, ['y' => 57, 'marker' => ['fillColor' => '#FFA500']]];
        $d2 = [110, 30, ['y' => 41, 'marker' => ['fillColor' => '#FFA500']]];

        return [
            array_column($data, 'time'),
            [
                [
                    'name' => 'Systolic',
                    'data' => array_column($data, 'systolic'),
                ],
                [
                    'name' => 'Dyastolic',
                    'data' => array_column($data, 'dyastolic'),
                ]
            ],
            $lastDate,
            $lastSystolic,
            $lastDyastolic,
            $range
        ];
    }

    public function heartRate($query, $range)
    {
        $data = $this->vitalsData($query, $range, 'heart_rate');

        $count = count($data);
        $lastDate = 0;
        $lastHeartRate = 0;

        if ($count > 0) {
            $lastDate = $data[count($data) - 1]['time'];
            $lastHeartRate = $data[count($data) - 1]['heart_rate'];
        }

        return [
            array_column($data, 'time'),
            [
                [
                    'name' => 'BPM',
                    'data' => array_column($data, 'heart_rate'),
                ]
            ],
            $lastDate,
            $lastHeartRate,
            $range
        ];
    }

    public function temperature($query, $range)
    {
        $data = $this->vitalsData($query, $range, 'temperature');

        $count = count($data);
        $lastDate = 0;
        $lastTemperature = 0;

        if ($count > 0) {
            $lastDate = $data[count($data) - 1]['time'];
            $lastTemperature = $data[count($data) - 1]['temperature'];
        }

        return [
            array_column($data, 'time'),
            [
                [
                    'name' => 'Temperature',
                    'data' => array_column($data, 'temperature'),
                ]
            ],
            $lastDate,
            $lastTemperature,
            $range
        ];
    }

    public function saturation($query, $range)
    {
        $data = $this->vitalsData($query, $range, 'oxygen_saturation');

        $count = count($data);
        $lastDate = 0;
        $lastSaturation = 0;

        if ($count > 0) {
            $lastDate = $data[count($data) - 1]['time'];
            $lastSaturation = $data[count($data) - 1]['oxygen_saturation'];
        }

        return [
            array_column($data, 'time'),
            [
                [
                    'name' => 'O2 Saturation',
                    'data' => array_column($data, 'oxygen_saturation'),
                ]
            ],
            $lastDate,
            $lastSaturation,
            $range
        ];
    }

    public function respiratory($query, $range)
    {
        $data = $this->vitalsData($query, $range, 'respiratory');

        $count = count($data);
        $lastDate = 0;
        $lastRespiratory = 0;

        if ($count > 0) {
            $lastDate = $data[count($data) - 1]['time'];
            $lastRespiratory = $data[count($data) - 1]['respiratory'];
        }

        return [
            array_column($data, 'time'),
            [
                [
                    'name' => 'Respiratory Rate',
                    'data' => array_column($data, 'respiratory'),
                ]
            ],
            $lastDate,
            $lastRespiratory,
            $range
        ];
    }

}
