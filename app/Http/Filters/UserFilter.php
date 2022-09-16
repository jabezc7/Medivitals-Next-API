<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserFilter implements Filterable
{
    public function apply(Builder $builder, $filters)
    {
        $builder->whereHas('groups', function ($q) {
            $q->where('slug', '<>', 'member');
        });
    }
}
