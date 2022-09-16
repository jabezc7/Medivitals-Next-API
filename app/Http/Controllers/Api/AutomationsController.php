<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\AutomationFilter;
use App\Http\Resources\Automation\AutomationCollection;
use App\Http\Resources\Automation\AutomationResource;
use App\Models\Automation;
use App\Policies\AutomationPolicy;
use Illuminate\Database\Eloquent\Model;

class AutomationsController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Automation::class;

    protected string $policy = AutomationPolicy::class;

    protected string $collection = AutomationCollection::class;

    protected string $resource = AutomationResource::class;

    protected string $filter = AutomationFilter::class;
}
