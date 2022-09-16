<?php

namespace App\Policies;

use App\Models\Section;

class SectionPolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Section::class;
    }

    protected function getResourceSlug(): string
    {
        return 'sections';
    }
}
