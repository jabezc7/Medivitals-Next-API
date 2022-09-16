<?php

namespace App\Http\Filters;

use App\Models\Type;
use Illuminate\Database\Eloquent\Builder;

class DataFilter implements Filterable
{
    public function apply(Builder $builder, $filters)
    {
        if (isset($filters->search) && $filters->search->value != '') {
            $search = $filters->search->value;
            $builder->where(function ($query) use ($search) {
                $query->where('patient_id', $search);
            });
        }

        if (isset($filters->type)) {
            if (is_string($filters->type) && $filters->type != '') {
                $type = Type::getTypeNameByID($filters->type);

                $newType = strtolower((str_replace('-', '_', $type)));
                $builder->where('type', $newType);
            }
        }

        if (isset($filters->date)) {
            $startDate = $filters->date->startDate;
            $endDate = $filters->date->endDate;

            $builder->whereBetween('created_at', [$startDate, $endDate]);

        }
    }
}
