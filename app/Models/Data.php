<?php

namespace App\Models;

use App\Events\DataCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Filters\Filterable;

class Data extends Model
{
    use HasFactory;

    protected $dispatchesEvents = [
        'created' => DataCreated::class
    ];

    protected $fillable = [
        'payload_id',
        'device_id',
        'patient_id',
        'type',
        'value',
        'created_at'
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function payload(): BelongsTo
    {
        return $this->belongsTo(Payload::class);
    }

    public function scopeFilter(Builder $builder, Filterable $filter, $filters)
    {
        $filter->apply($builder, $filters);
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
}
