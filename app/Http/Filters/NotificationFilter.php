<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class NotificationFilter implements Filterable
{
    public function apply(Builder $builder, $filters)
    {
        $builder->when($filters->alert->value,
            fn () => $builder->where('alert', $filters->alert->value)
        );

        $builder->when($filters->priority->value && $filters->priority->value !== 'null',
            fn () => $builder->where('priority', $filters->priority->value)
        );
    }
}
