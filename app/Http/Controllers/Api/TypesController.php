<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\TypeFilter;
use App\Http\Resources\Type\TypeCollection;
use App\Http\Resources\Type\TypeResource;
use App\Models\Type;
use App\Policies\TypePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TypesController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Type::class;

    protected string $policy = TypePolicy::class;

    protected string $collection = TypeCollection::class;

    protected string $resource = TypeResource::class;

    protected string $filter = TypeFilter::class;

    public function destroy(string $id): JsonResponse
    {
        if ($model = $this->model::query()->find($id)) {
            $this->authoriseAction('delete', $model);

            if (!$model->locked) {
                $model->delete();
                return response()->json(['success' => true, 'message' => 'Successfully Deleted']);
            } else {
                return response()->json(['success' => false, 'message' => 'Type locked and cannot be deleted']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Not Found'], 404);
        }
    }

    public function copy($id): JsonResponse
    {
        if ($type = Type::query()->find($id)) {
            $copy = $type->replicate();
            $copy->locked = false;
            $copy->save();

            return response()->json(['success' => true, 'message' => 'Type cloned successfully', 'data' => $copy]);
        }

        return response()->json(['success' => false, 'message' => 'Unable to clone type']);
    }

    public function groups(): JsonResponse
    {
        $groups = Type::query()->select('group')->groupBy('group')->get();
        return response()->json($groups);
    }

    public function formData(): JsonResponse
    {
        $return['groups'] = Type::query()->select('group')->groupBy('group')->get();
        $return['types'] = Type::query()->select(['id', 'name', 'group', 'ordering'])
            ->where('active', 1)
            ->orderBy('group', 'ASC')
            ->orderBy('ordering', 'ASC')
            ->get();

        return response()->json($return);
    }

    public function storeGroup(Request $request): JsonResponse
    {
        $options = preg_split("/\r\n|\n|\r/", $request->get('options'));

        if ($options && count($options) > 0) {
            foreach ($options as $option) {
                Type::query()->create([
                    'name' => $option,
                    'group' => Str::slug($request->get('group')),
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Successfully Created Group']);
    }
}
