<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class GroupFilter implements Filterable
{
    public function apply(Builder $builder, $filters)
    {
        if (isset($filters->search) && $filters->search->value != '') {
            $search = $filters->search->value;
            $builder->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if (isset($filters->type)) {
            if (is_string($filters->type) && $filters->type != '') {
                $builder->where('type_id', $filters->type);
            } elseif (is_object($filters->type) && isset($filters->type->value) && $filters->type->value != '') {
                $builder->where('type_id', $filters->type->value);
            }
        }
    }
}
