<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class SectionFilter implements Filterable
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
    }
}
