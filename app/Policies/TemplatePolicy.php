<?php

namespace App\Policies;

use App\Models\Template;

class TemplatePolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Template::class;
    }

    protected function getResourceSlug(): string
    {
        return 'templates';
    }
}
