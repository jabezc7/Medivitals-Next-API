<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

interface Filterable
{
    public function apply(Builder $builder, $filters);
}
