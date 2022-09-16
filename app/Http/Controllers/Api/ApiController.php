<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class ApiController extends Controller
{
    /**
     * Model Class this controller caters.
     *
     * @var string
     */
    protected string $modelClass;

    /**
     * Policy Class this controller refers to.
     *
     * @var string
     */
    protected string $policyClass;

    /**
     * Collection Class the above assigned Model uses.
     *
     * @var string
     */
    protected string $collectionClass;

    /**
     * Simple Collection Class the above assigned Model uses.
     *
     * @var string
     */
    protected string $simpleCollectionClass;

    /**
     * Resource Class the above assigned Model uses.
     *
     * @var string
     */
    protected string $resourceClass;

    /**
     * Filter Class the above assigned Model uses.
     *
     * @var string
     */
    protected string $filterClass;

    /**
     * Request instance.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * Get Model Class this controller caters.
     *
     * @return string
     */
    abstract protected function getModelClass() : string;

    /**
     * Get Policy Class this controller refers to.
     *
     * @return string
     */
    abstract protected function getPolicyClass() : string;

    /**
     * Get Collection Class the above assigned Model uses.
     *
     * @return string
     */
    abstract protected function getSimpleCollectionClass() : string;

    /**
     * Get Collection Class the above assigned Model uses.
     *
     * @return string
     */
    abstract protected function getCollectionClass() : string;

    /**
     * Get Resource Class the above assigned Model uses.
     *
     * @return string
     */
    abstract protected function getResourceClass() : string;

    /**
     * Get Filter Class the above assigned Model uses.
     *
     * @return string
     */
    abstract protected function getFilterClass() : string;


    /**
     * ApiController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->modelClass = $this->getModelClass();
        $this->policyClass = $this->getPolicyClass();
        $this->collectionClass = $this->getCollectionClass();
        $this->simpleCollectionClass = $this->getSimpleCollectionClass();
        $this->resourceClass = $this->getResourceClass();
        $this->filterClass = $this->getFilterClass();
        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection|JsonResponse
     * @throws AuthorizationException
     */
    public function index(): ResourceCollection|JsonResponse
    {
        $this->authoriseAction('index');

        if ($this->request->get('response') && $this->request->get('response') == 'datatable') {
            return new $this->collectionClass($this->modelClass::data($this->filterClass, $this->request, 'datatable'));
        } else {
            $models = $this->modelClass::data($this->filterClass, $this->request);

            if ($models){
                return response()->json(new $this->collectionClass($models));
            } else {
                return response()->json([], 404);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(): JsonResponse
    {
        $this->authoriseAction('create');

        $model = new $this->modelClass();
        $model->fill($this->request->all());
        $model->save();

        if($this->request->get('tags')) {
            $model->syncTags($this->request->get('tags'));
        }

        return response()->json(['success' => true, 'message' => 'Successfully Created', 'data' => new $this->resourceClass($model)]);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(string $id): JsonResponse
    {
        if ($model = $this->modelClass::find($id)) {
            $this->authoriseAction('view', $model);

            $model = $this->modelClass::where('id', $id)->when($this->request->get('with'), function($query) {
                $withs = explode(',', $this->request->get('with'));

                foreach ($withs as $with) {
                    $query->with($with);
                }
            })->first();

            return response()->json(new $this->resourceClass($model));
        } else {
            return response()->json([], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(string $id): JsonResponse
    {
        if ($model = $this->modelClass::find($id)) {
            $this->authoriseAction('update', $model);

            $model->fill($this->request->all());
            $model->save();

            if($this->request->get('tags')) {
                $model->syncTags($this->request->get('tags'));
            }

            return response()->json(['success' => true, 'message' => 'Successfully Updated', 'data' => new $this->resourceClass($model)]);
        } else {
            return response()->json(['success' => false, 'message' => 'Not Found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(string $id): JsonResponse
    {
        if ($model = $this->modelClass::find($id)) {

            $this->authoriseAction('delete', $model);

            try {
                $model->delete();
            } catch(QueryException $e){
                if ($e->getCode() === '23000'){
                    return response()->json(['success' => false, 'message' => 'Unable to delete a record that has been used']);
                }

                return response()->json(['success' => false, 'message' => 'Error Deleting Record']);
            }

            return response()->json(['success' => true, 'message' => 'Successfully Deleted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Not Found'], 404);
        }
    }

    /**
     * Toggle column.
     *
     * @param string $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function toggle(string $id): JsonResponse
    {
        if ($model = $this->modelClass::find($id)) {

            $this->authoriseAction('update', $model);

            $column = $this->request->get('field');

            if ($model->getConnection()
                ->getSchemaBuilder()
                ->hasColumn($model->getTable(), $column)) {

                $model->{$column} = !$model->{$column};
                $model->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Column Not Found'], 500);
            }

            return response()->json(['success' => true, 'message' => 'Toggled Successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Not Found'], 404);
        }
    }

    /**
     * Attach relational resource to this resource.
     *
     * @param string $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function attach(string $id): JsonResponse
    {
        if ($model = $this->modelClass::find($id)) {

            $this->authoriseAction('update', $model);

            if (!$this->request->has('type')) {
                return response()->json(['success' => false, 'message' => 'Parameter Type Is Required'], 500);
            }

            if (!$this->request->has('id')) {
                return response()->json(['success' => false, 'message' => 'Parameter ID Is Required'], 500);
            }

            $relationships = $this->request->get('type');

            if ($model->$relationships->contains($this->request->get('id'))) {
                return response()->json(['success' => false, 'message' => 'Already Attached'], 500);
            }

            try {
                $model->$relationships()->attach($this->request->get('id'));
            } catch(QueryException $exception) {
                return response()->json(['success' => false, 'message' => 'An Error Occurred'], 500);
            }

            $result = [
                'success' => true,
                'message' => 'Attached successfully'
            ];

            if ($this->request->get('return') == 'array') {
                $result['data'] = new $this->resourceClass($model->fresh());
            }

            return response()->json($result);
        } else {
            return response()->json(['success' => false, 'message' => 'Not Found'], 404);
        }
    }

    /**
     * Detach relational resource to this resource.
     *
     * @param string $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function detach(string $id): JsonResponse
    {
        if ($model = $this->modelClass::find($id)) {

            $this->authoriseAction('update', $model);

            if (!$this->request->has('type')) {
                return response()->json(['success' => false, 'message' => 'Parameter Type Is Required'], 500);
            }

            if (!$this->request->has('id')) {
                return response()->json(['success' => false, 'message' => 'Parameter ID Is Required'], 500);
            }

            $relationships = $this->request->get('type');

            if (!$model->$relationships->contains($this->request->get('id'))) {
                return response()->json(['success' => false, 'message' => 'Already Removed'], 500);
            }

            try {
                $model->$relationships()->detach($this->request->get('id'));
            } catch(QueryException $exception) {
                return response()->json(['success' => false, 'message' => 'An error occurred'], 500);
            }

            $result = [
                'success' => true,
                'message' => 'Removed Successfully'
            ];

            if ($this->request->get('return') == 'array') {
                $result['data'] = new $this->resourceClass($model->fresh());
            }

            return response()->json($result);
        } else {
            return response()->json(['success' => false, 'message' => 'Not Found'], 404);
        }
    }

    /**
     * Authorise action.
     *
     * @param $action
     * @param null $model
     * @throws AuthorizationException
     */
    protected function authoriseAction($action, $model = null)
    {
        if (class_exists($this->policyClass)) {
            $this->authorize($action, is_null($model) ? $this->modelClass : $model);
        }
    }
}
