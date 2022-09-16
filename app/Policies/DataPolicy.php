<?php

namespace App\Policies;

use App\Models\Data;

class DataPolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Data::class;
    }

    protected function getResourceSlug(): string
    {
        return 'data';
    }
}
