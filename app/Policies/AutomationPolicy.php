<?php

namespace App\Policies;

use App\Models\Automation;

class AutomationPolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Automation::class;
    }

    protected function getResourceSlug(): string
    {
        return 'automations';
    }
}
