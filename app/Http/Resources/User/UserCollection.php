<?php

namespace App\Http\Resources\User;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class UserCollection extends ResourceCollection
{
    public function toArray($request): array|Collection|\JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($user) {
            return [
                'id' => $user->id,
                'slug' => $user->slug,
                'first' => $user->first,
                'last' => strtoupper($user->last),
                'full_name' => $user->first.' '.strtoupper($user->last),
                'full_name_reversed' => strtoupper($user->last).', '.$user->first,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'phone' => $user->phone,
                'signature' => $user->signature,
                'groups' => $user->groups
            ];
        });
    }
}
