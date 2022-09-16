<?php

namespace App\Http\Resources\Notification;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class NotificationCollection extends ResourceCollection
{
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($notification) {
            return [
                'id' => $notification->id,
                'patient' => optional($notification->patient)->only(['id', 'first', 'last']),
                'message' => $notification->message,
                'alert' => $notification->alert,
                'triggers' => $notification->triggers,
                'priority' => $notification->priority,
                'meta' => [
                    'created_at' => $notification->created_at,
                    'created_at_human' => $notification->created_at->diffForHumans()
                ],
            ];
        });
    }
}
