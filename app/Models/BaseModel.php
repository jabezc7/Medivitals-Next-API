<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Filters\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

abstract class BaseModel extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected bool $keyIsUuid = true;

    protected int $uuidVersion = 4;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if ($model->keyIsUuid && empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = $model->generateUuid();
            }
        });
    }

    public function scopeFilter(Builder $builder, Filterable $filter, $filters)
    {
        $filter->apply($builder, $filters);
    }

    public static function data($filterClass, $request, $type = null, $select = null, string $sort_column = null, string $sort_direction = 'ASC'): mixed
    {
        $sortColumn = $request->get('sortColumn') ? $request->get('sortColumn') : $sort_column;
        $sortDir = $request->get('sortDir') ? $request->get('sortDir') : $sort_direction;
        $filters = json_decode($request->get('filters'));
        $params = is_array($request->get('query')) ? $request->get('query') : json_decode($request->get('query'));

        $query = self::when($select, function ($query) use ($select) {
            $query->select($select);
        })->when($sortColumn && $sortDir, function ($query) use ($request, $sortColumn, $sortDir) {
            $query->orderBy($sortColumn, $sortDir);
        })->where(function ($query) use ($filters, $request, $filterClass, $params) {
            if ($filterClass) {
                $query->filter(new $filterClass, $filters);
            }

            // Query Params
            if ($params && count($params) > 0) {
                foreach ($params as $param) {
                    if (is_string($param)){
                        $param = json_decode($param);
                    }

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

    protected function generateUuid(): string
    {
        switch ($this->uuidVersion) {
            case 1:
                return Uuid::uuid1()->toString();
            case 4:
                return Uuid::uuid4()->toString();
        }

        throw new Exception("UUID version [{$this->uuidVersion}] not supported.");
    }
}
