<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\GroupFilter;
use App\Http\Resources\Group\GroupCollection;
use App\Http\Resources\Group\GroupResource;
use App\Models\Group;
use App\Models\Type;
use App\Policies\GroupPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class GroupsController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Group::class;

    protected string $policy = GroupPolicy::class;

    protected string $collection = GroupCollection::class;

    protected string $resource = GroupResource::class;

    protected string $filter = GroupFilter::class;

    public function store() : JsonResponse
    {
        $this->authoriseAction('create');

        $model = new $this->model();
        $model->fill($this->request->all());
        $model->save();

        $permissions = [];
        $sections = [];

        if ($this->request->get('permissions')) {
            $permissions = $this->request->get('permissions');
        }

        $model->permissions()->sync($permissions);

        if ($this->request->get('sections')) {
            $sections = $this->request->get('sections');
        }

        $model->sections()->sync($sections);

        return response()->json([
            'success' => true,
            'message' => 'Successfully Created',
            'data' => new $this->resource($model)
        ]);
    }

    public function update(string $id) : JsonResponse
    {
        if ($model = $this->model::query()->find($id)) {
            $this->authoriseAction('update');

            $model->fill($this->request->all());
            $model->save();

            $permissions = [];
            $sections = [];

            if ($this->request->get('permissions')) {
                $permissions = $this->request->get('permissions');
                $model->permissions()->sync($permissions);
            }

            if ($this->request->get('sections')) {
                $sections = $this->request->get('sections');
                $model->sections()->sync($sections);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully Updated',
                'data' => new $this->resource($model)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Not Found'
            ], 404);
        }
    }

    public function formData(): JsonResponse
    {
        $return['types'] = Type::lookup('group-types');
        return response()->json($return);
    }
}
