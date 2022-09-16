<?php

namespace App\Policies;

use App\Models\Permission;

class PermissionPolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Permission::class;
    }

    protected function getResourceSlug(): string
    {
        return 'groups';
    }
}
