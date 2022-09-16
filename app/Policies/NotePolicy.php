<?php

namespace App\Policies;

use App\Models\Note;

class NotePolicy extends BasePolicy
{
    protected function getModelClass(): string
    {
        return Note::class;
    }

    protected function getResourceSlug(): string
    {
        return 'notes';
    }
}
