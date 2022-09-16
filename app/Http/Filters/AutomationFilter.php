<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class AutomationFilter implements Filterable
{
    public function apply(Builder $builder, $filters)
    {
        //if (isset($filters->search) && $filters->search->value != '') {
        //    $search = $filters->search->value;
        //    $builder->where(function ($query) use ($search) {
        //        $query->where('id', 'like', '%'.$search.'%')
        //            ->orWhere('name', 'like', '%'.$search.'%');
        //    });
        //}
    }
}
