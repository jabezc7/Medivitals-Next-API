<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mustache_Engine;

class Sms extends Model
{
    use Uuid, SoftDeletes;

    protected $table = 'sms';

    protected $fillable = [
        'provider_id',
        'to',
        'patient_id',
        'from',
        'message',
        'direction',
        'created_by',
        'scheduled',
        'scheduled_at'
    ];

    public $incrementing = false;

    public $timestamps = true;

    public $appends = [
        'formatted_from_number',
        'formatted_to_number'
    ];

    public $casts = [
        'scheduled' => 'boolean',
        'scheduled_at' => 'datetime'
    ];

    public function smsable(): MorphTo
    {
        return $this->morphTo();
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'smsable');
    }

    public function patient(): MorphToMany
    {
        return $this->morphedByMany(Patient::class, 'smsable');
    }

    public function patientInfo(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function getFormattedToNumberAttribute(): ?string
    {
        if ($this->to){
            return $this->_formatMobileNumber($this->to);
        }

        return null;
    }

    public function getFormattedFromNumberAttribute(): ?string
    {
        if ($this->from){
            return $this->_formatMobileNumber($this->from);
        }

        return null;
    }

    private function _formatMobileNumber($value): string
    {
        $number = str_replace('+614', '04', $value);

        if (str_contains($number, '04')) {
            return substr($number, 0, 4) . ' ' . substr($number, 4, 3) . ' ' . substr($number, 7, 3);
        } else {
            return $number;
        }
    }

    public static function parseMessage($message, $object): string
    {
        $m = new Mustache_Engine(array('entity_flags' => ENT_QUOTES));
        return $m->render(
            $message,
            array(
                'patient-first' => $object->first,
                'patient-last' => $object->last,
            )
        );
    }
}
