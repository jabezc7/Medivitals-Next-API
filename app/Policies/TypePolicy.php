<?php

namespace App\Policies;

use App\Models\Type;

class TypePolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Type::class;
    }

    protected function getResourceSlug(): string
    {
        return 'types';
    }
}
