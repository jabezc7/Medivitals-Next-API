<?php

namespace App\Policies;

use App\Models\Notification;

class NotificationPolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Notification::class;
    }

    protected function getResourceSlug(): string
    {
        return 'notifications';
    }
}
