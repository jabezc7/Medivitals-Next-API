<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class TypeFilter implements Filterable
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

        if (isset($filters->group)) {
            if (is_string($filters->group) && $filters->group != '') {
                $builder->where('group', $filters->group);
            } elseif (is_object($filters->group) && isset($filters->group->value) && $filters->group->value != '') {
                $builder->where('group', $filters->group->value);
            }
        }
    }
}
