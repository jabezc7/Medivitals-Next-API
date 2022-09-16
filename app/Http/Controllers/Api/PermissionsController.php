<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Permission\PermissionCollection;
use App\Http\Resources\Permission\PermissionResource;
use App\Models\Permission;
use App\Models\Type;
use App\Policies\PermissionPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Permission::class;

    protected string $policy = PermissionPolicy::class;

    protected string $collection = PermissionCollection::class;

    protected string $resource = PermissionResource::class;

    protected string $filter = '';

    public function formData(): JsonResponse
    {
        $return['types'] = Type::lookup('permission-types');
        return response()->json($return);
    }
}
