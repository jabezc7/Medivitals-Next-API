<?php

namespace App\Policies;

use App\Models\Group;

class GroupPolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Group::class;
    }

    protected function getResourceSlug(): string
    {
        return 'groups';
    }
}
