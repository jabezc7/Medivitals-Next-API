<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return User::class;
    }

    protected function getResourceSlug(): string
    {
        return 'users';
    }
}
