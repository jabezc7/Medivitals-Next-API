<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PatientScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereHas('groups', function ($q) {
            $q->where('slug', 'patient');
        });
    }
}
