<?php

namespace App\Policies;

use App\Models\Device;

class DevicePolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Device::class;
    }

    protected function getResourceSlug(): string
    {
        return 'devices';
    }
}
