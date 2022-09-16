<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait CrudTrait
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(): ResourceCollection|JsonResponse
    {
        $this->authoriseAction('index');

        if ($this->request->get('response') && $this->request->get('response') == 'datatable') {
            return new $this->collection($this->model::data($this->filter, $this->request, 'datatable'));
        } else {
            $models = $this->model::data($this->filter ?? null, $this->request);

            if ($models){
                return response()->json(new $this->collection($models));
            } else {
                return response()->json([], 404);
            }
        }
    }

    public function store(): JsonResponse
    {
        $this->authoriseAction('create');

        $model = new $this->model();
        $model->fill($this->request->all());
        $model->save();

        if($this->request->get('tags')) {
            $model->syncTags($this->request->get('tags'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully Created',
            'data' => $this->resource ? new $this->resource($model) : $model
        ]);
    }

    public function show(string $id): JsonResponse
    {
        if ($model = $this->model::query()->find($id)) {
            $this->authoriseAction('view', $model);

            $model = $this->model::query()->where('id', $id)->when($this->request->get('with'), function($query) {
                $withs = explode(',', $this->request->get('with'));

                foreach ($withs as $with) {
                    $query->with($with);
                }
            })->first();

            return response()->json(new $this->resource($model));
        } else {
            return response()->json([], 404);
        }
    }

    public function update(string $id): JsonResponse
    {
        if ($model = $this->model::query()->find($id)) {
            $this->authoriseAction('update', $model);

            $model->fill($this->request->all());
            $model->save();

            return response()->json([
                'success' => true,
                'message' => 'Successfully Updated',
                'data' => $this->resource ? new $this->resource($model->fresh()) : $model->fresh()
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Not Found'
            ], 404);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        if ($model = $this->model::query()->find($id)) {
            $this->authoriseAction('delete', $model);

            try {
                $model->delete();
            } catch(QueryException $e){
                if ($e->getCode() === '23000'){
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to delete a record that has been used'
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Error Deleting Record'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully Deleted'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Not Found'
            ], 404);
        }
    }

    public function toggle(string $id): JsonResponse
    {
        if ($model = $this->model::query()->find($id)) {

            $this->authoriseAction('update', $model);

            $column = $this->request->get('field');

            if ($model->getConnection()
                ->getSchemaBuilder()
                ->hasColumn($model->getTable(), $column)) {

                $model->{$column} = !$model->{$column};
                $model->save();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Column Not Found'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Toggled Successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Not Found'
            ], 404);
        }
    }

    public function attach(string $id): JsonResponse
    {
        if ($model = $this->model::query()->find($id)) {

            $this->authoriseAction('update', $model);

            if (!$this->request->has('type')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter Type Is Required'
                ], 500);
            }

            if (!$this->request->has('id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter ID Is Required'
                ], 500);
            }

            $relationships = $this->request->get('type');

            if ($model->$relationships->contains($this->request->get('id'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already Attached'
                ], 500);
            }

            try {
                $model->$relationships()->attach($this->request->get('id'));
            } catch(QueryException $exception) {
                return response()->json([
                    'success' => false,
                    'message' => 'An Error Occurred'
                ], 500);
            }

            $result = [
                'success' => true,
                'message' => 'Attached successfully'
            ];

            if ($this->request->get('return') == 'array') {
                $result['data'] = new $this->resource($model->fresh());
            }

            return response()->json($result);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Not Found'
            ], 404);
        }
    }

    public function detach(string $id): JsonResponse
    {
        if ($model = $this->model::query()->find($id)) {

            $this->authoriseAction('update', $model);

            if (!$this->request->has('type')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter Type Is Required'
                ], 500);
            }

            if (!$this->request->has('id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter ID Is Required'
                ], 500);
            }

            $relationships = $this->request->get('type');

            if (!$model->$relationships->contains($this->request->get('id'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already Removed'
                ], 500);
            }

            try {
                $model->$relationships()->detach($this->request->get('id'));
            } catch(QueryException $exception) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred'
                ], 500);
            }

            $result = [
                'success' => true,
                'message' => 'Removed Successfully'
            ];

            if ($this->request->get('return') == 'array') {
                $result['data'] = new $this->resource($model->fresh());
            }

            return response()->json($result);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Not Found'
            ], 404);
        }
    }

    protected function authoriseAction($action, $model = null)
    {
        if (class_exists($this->policy)) {
            $this->authorize($action, is_null($model) ? $this->model : $model);
        }
    }
}
