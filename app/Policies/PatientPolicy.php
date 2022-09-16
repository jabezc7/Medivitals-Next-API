<?php

namespace App\Policies;

use App\Models\Patient;

class PatientPolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Patient::class;
    }

    protected function getResourceSlug(): string
    {
        return 'patients';
    }
}
