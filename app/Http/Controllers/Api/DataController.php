<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\DataFilter;
use App\Http\Resources\Data\DataCollection;
use App\Http\Resources\Data\DataResource;
use App\Models\Data;
use App\Policies\DataPolicy;
use Illuminate\Database\Eloquent\Model;

class DataController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Data::class;

    protected string $policy = DataPolicy::class;

    protected string $collection = DataCollection::class;

    protected string $resource = DataResource::class;

    protected string $filter = DataFilter::class;

}
