<?php

namespace App\Policies;

use App\Models\Attachment;

class AttachmentPolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Attachment::class;
    }

    protected function getResourceSlug(): string
    {
        return 'attachments';
    }
}
