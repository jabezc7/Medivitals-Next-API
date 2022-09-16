<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\UserFilter;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserSimpleCollection;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    use CrudTrait;

    protected string|Model $model = User::class;

    protected string $policy = UserPolicy::class;

    protected string $collection = UserCollection::class;

    protected string $resource = UserResource::class;

    protected string $filter = UserFilter::class;

    public function store() : JsonResponse
    {
        $this->authoriseAction('create');

        $model = new $this->model();
        $model->fill($this->request->all());
        $model->save();

        // Save Permissions
        $this->savePermissions($model);

        return response()->json(['success' => true, 'message' => 'Successfully Created', 'data' => new $this->resource($model)]);
    }
    public function updateUserSettings() : JsonResponse
    {
        $validator =  Validator::make($this->request->all(), [
            'email' => 'email|unique:users',
            'password' => 'min:6|confirmed',
            'password_confirmation' => 'min:6'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        } else {
            $model = Member::query()->find(auth()->user()->id);
            $model->fill($this->request->all());
            $model->save();

            return response()->json([
                'success' => false,
                'message' => 'User settings was successfully updated!'
            ]);
        }
    }
    public function update(string $id) : JsonResponse
    {
        if ($model = $this->model::query()->find($id)) {
            $this->authoriseAction('update');

            $model->fill($this->request->all());
            $model->save();

            // Save Permissions
            $this->savePermissions($model);

            return response()->json(['success' => true, 'message' => 'Successfully Updated', 'data' => new $this->resource($model)]);
        } else {
            return response()->json(['success' => false, 'message' => 'Not Found'], 404);
        }
    }

    public function savePermissions(mixed $model): void
    {
        $permissions = [];

        if ($this->request->get('permissions')) {
            $permissions = $this->request->get('permissions');
        }

        $model->permissions()->sync($permissions);

        // Save Sections
        $sections = [];

        if ($this->request->get('sections')) {
            $sections = $this->request->get('sections');
        }

        $model->sections()->sync($sections);

        // Save Groups
        $groups = [];

        if ($this->request->get('groups')) {
            $groups = $this->request->get('groups');
        }

        $model->groups()->sync($groups);
    }

    public function get() {
        $users = User::select(['id', 'first', 'last'])->get();
        return response()->json(new UserSimpleCollection($users));
    }
}
