<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordReset extends BaseModel
{
    protected $table = 'password_resets';

    protected $fillable = [
        'user_id',
        'email',
        'token',
        'created_at'
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
