<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\NoteFilter;
use App\Http\Resources\Note\NoteCollection;
use App\Http\Resources\Note\NoteResource;
use App\Models\Note;
use App\Policies\NotePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotesController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Note::class;

    protected string $policy = NotePolicy::class;

    protected string $collection = NoteCollection::class;

    protected string $resource = NoteResource::class;

    protected string $filter = NoteFilter::class;

    public function index(): ResourceCollection|JsonResponse
    {
        $this->authoriseAction('index');

        if ($this->request->get('model') && $this->request->get('model_id')){
            $modelNamespace = '\\App\\Models\\'.$this->request->get('model');
            $model = $modelNamespace::find($this->request->get('model_id'));

            return response()->json(new $this->collection($model->notes));
        } else {
            return response()->json([], 404);
        }
    }

    public function store(): JsonResponse
    {
        $this->authoriseAction('create');

        if ($this->request->get('parent_id')) {
            $note = new $this->model();
            $note->fill($this->request->all());
            $note->save();

            return response()->json(['success' => true, 'message' => 'Reply Added', 'data' => new $this->resource($note)]);
        } else {
            $modelNamespace = '\\App\\Models\\' . $this->request->get('model');
            $model = $modelNamespace::find($this->request->get('model_id'));

            if ($model) {
                $note = $model->notes()->create($this->request->all());

                return response()->json(['success' => true, 'message' => 'Successfully Created Note', 'data' => new $this->resource($note)]);
            } else {
                return response()->json(['success' => false, 'message' => 'Error saving note for '.$modelNamespace]);
            }
        }
    }

    public function getNotesByPatientID() {
        $notes = Note::where('noteable_id', $this->request->patient_id)->orderby('created_at', 'desc')->get();
        return response()->json(new $this->collection($notes));
    }
}
