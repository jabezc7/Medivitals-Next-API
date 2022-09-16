<?php

namespace App\Http\Resources\User;

use App\Http\Controllers\Api\AuthController;
use App\Http\Resources\Group\GroupRelationshipCollection;
use App\Http\Resources\Patient\PatientCollection;
use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public $combined;

    public function __construct($resource, $combined = true)
    {
        $this->combined = $combined;
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        $return = [
            'id' => $this->id,
            'slug' => $this->slug,
            'first' => $this->first,
            'last' => strtoupper($this->last),
            'full_name' => $this->first.' '.$this->last,
            'full_name_reversed' => strtoupper($this->last).', '.$this->first,
            'email' => $this->email,
            'company_name' => $this->company_name,
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'active' => $this->active,
            'position' => $this->position,
            'settings' => [],
            'avatar' => $this->avatar,
            'patient' => new UserSimpleResource($this->patient),
            'meta' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'activeSubscription' => $this->activeSubscription,
        ];

        foreach (Setting::all() as $setting){
            $return['settings'][$setting->key] = $setting->value;
        }

        if ($this->groups) {
            $return['groups'] = new GroupRelationshipCollection($this->groups);
        }

        if ($this->isSuperAdmin()) {
            $return['nimdarepus'] = true;
        } else {
            $return['nimdarepus'] = false;
        }

        if ($this->microsoft_refresh_token) {
            $return['email_provider'] = [
                'provider' => 'microsoft',
                'email' => $this->microsoft_account_email,
            ];
        } elseif ($this->google_refresh_token) {
            $return['email_provider'] = [
                'provider' => 'google',
                'email' => $this->google_account_email,
            ];
        } else {
            $return['email_provider'] = null;
        }

        $userAccessPermissions = AuthController::getAccessControl($return['nimdarepus'], $this, $this->combined);

        return array_merge($return, $userAccessPermissions);
    }
}
