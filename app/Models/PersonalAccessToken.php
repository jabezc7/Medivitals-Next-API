<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\PersonalAccessToken as BasePersonalAccessToken;

class PersonalAccessToken extends BasePersonalAccessToken
{
    public function tokenable(): MorphTo
    {
        return $this->morphTo('tokenable', 'tokenable_type', 'tokenable_uuid');
    }
}
