<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class PatientFilter implements Filterable
{
    public function apply(Builder $builder, $filters)
    {
//        $builder->when(!auth()->user()->isSuperAdmin(),
//            fn () => $builder->scopeOnlyRelatedMembers()
//        );

        if (isset($filters->search) && $filters->search->value != '') {
            $search = $filters->search->value;
            $builder->where(function ($query) use ($search) {
                $query->where('first', 'LIKE', '%'.$search.'%')
                    ->orWhere('last', 'LIKE', '%'.$search.'%')
                    ->orWhere('email', 'LIKE', '%'.$search.'%');
            });
        }

        $builder->when(isset($filters->devices) && $filters->devices->value === true,
            fn () => $builder->whereHas('devices')
        );
    }
}
