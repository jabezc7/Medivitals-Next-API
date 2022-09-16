<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\NotificationFilter;
use App\Http\Resources\Notification\NotificationCollection;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\Notification;
use App\Policies\NotificationPolicy;
use Illuminate\Database\Eloquent\Model;

class NotificationsController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Notification::class;

    protected string $policy = NotificationPolicy::class;

    protected string $collection = NotificationCollection::class;

    protected string $resource = NotificationResource::class;

    protected string $filter = NotificationFilter::class;
}
