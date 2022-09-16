<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Section\SectionCollection;
use App\Http\Resources\Section\SectionResource;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionsController extends Controller
{
    public function index(Request $request): array|JsonResponse
    {
        $result = [];
        $controls = null;

        if (! auth()->user()->isSuperAdmin()) {
            if (! $controls = AuthController::getAccessControl()) {
                $controls['sections'] = [];
            }
        }

        $filters = json_decode($request->get('filters'));

        $sections = Section::query()->whereNull('parent_id')
            ->when(isset($controls['sections']), function ($query) use ($controls) {
                $query->whereIn('id', $controls['sections']);
            })
            ->where(function ($query) use ($filters, $request) {
                if (isset($filters->search) && $filters->search->value != '') {
                    $query->where(function ($q) use ($filters) {
                        $q->where('name', 'like', '%'.$filters->search->value.'%')
                            ->orWhere('slug', 'like', '%'.$filters->search->value.'%')
                            ->orWhere('route', 'like', '%'.$filters->search->value.'%');
                    });
                }

                if ($request->get('active')) {
                    $query->where('active', $request->get('active'));
                }
            })
            ->orderBy('ordering', 'ASC')->get();

        foreach ($sections as $section) {
            $result[] = $section;
            $childSections = Section::query()->where('parent_id', $section->id)
                ->when(isset($controls['sections']), function ($query) use ($controls) {
                    $query->whereIn('id', $controls['sections']);
                })
                ->orderBy('ordering', 'ASC')
                ->get();

            if ($childSections) {
                foreach ($childSections as $childSection) {
                    $result[] = $childSection;
                    $childChildSections = Section::query()->where('parent_id', $childSection->id)
                        ->orderBy('ordering', 'ASC')
                        ->when(isset($controls['sections']), function ($query) use ($controls) {
                            $query->whereIn('id', $controls['sections']);
                        })
                        ->get();

                    if ($childChildSections) {
                        foreach ($childChildSections as $childChildSection) {
                            $result[] = $childChildSection;
                        }
                    }
                }
            }
        }
        if ($request->get('response') == 'datatable') {
            $return['data'] = new SectionCollection($result);
            $return['from'] = 1;
            $return['last_page'] = 1;
            $return['per_page'] = 500;
            $return['to'] = count($result);
            $return['total'] = count($result);

            return response()->json($return);
        } elseif ($request->get('response') == 'array') {
            return $result;
        } else {
            return response()->json(new SectionCollection($result));
        }
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Section::class);

        $section = new Section();
        $section->fill($request->all());

        if ($request->get('parent_id')) {
            $parent = Section::query()->find($request->get('parent_id'));
            $section->level = $parent->level + 1;

            if ($lastChild = Section::query()->where('parent_id', $parent->id)->orderBy('ordering', 'DESC')->limit(1)->first()) {
                $section->ordering = $lastChild->ordering + 1;
            } else {
                $section->ordering = 1;
            }
        } else {
            $section->level = 1;

            if ($lastChild = Section::query()->whereNull('parent_id')->orderBy('ordering', 'DESC')->limit(1)->first()) {
                $section->ordering = $lastChild->ordering + 1;
            } else {
                $section->ordering = 1;
            }
        }

        $section->save();

        return response()->json(['success' => true, 'message' => 'Successfully Created Section', 'data' => new SectionResource($section)]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        if ($section = Section::query()->find($id)) {
            $this->authorize('view', $section);
            $section = Section::query()->where('id', $id)->when($request->get('with'), function ($query) use ($request) {
                $withs = explode(',', $request->get('with'));

                foreach ($withs as $with) {
                    $query->with($with);
                }
            })->first();

            return response()->json(new SectionResource($section));
        } else {
            return response()->json([], 404);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $section = Section::query()->find($id);
        $this->authorize('update', $section);

        if ($request->get('parent_id') && $section->parent_id != $request->get('parent_id')) {
            $parent = Section::query()->find($request->get('parent_id'));
            $section->level = $parent->level + 1;

            if ($lastChild = Section::query()->where('parent_id', $parent->id)->orderBy('ordering', 'DESC')->limit(1)->first()) {
                $section->ordering = $lastChild->ordering + 1;
            } else {
                $section->ordering = 1;
            }
        } elseif ($section->parent_id != $request->get('parent_id')) {
            $section->level = 1;

            if ($lastChild = Section::query()->whereNull('parent_id')->orderBy('ordering', 'DESC')->limit(1)->first()) {
                $section->ordering = $lastChild->ordering + 1;
            } else {
                $section->ordering = 1;
            }
        }

        $section->fill($request->all());
        $section->save();

        return response()->json(['success' => true, 'message' => 'Successfully Updated Section', 'data' => new SectionResource($section)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $section = Section::query()->find($id);
        $this->authorize('delete', $section);

        $section->groups()->sync([]);
        $section->users()->sync([]);

        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted successfully']);
    }

    public function toggle(Request $request, $id): JsonResponse
    {
        $section = Section::query()->find($id);
        $this->authorize('update', $section);

        $column = $request->get('field');
        $section->{$column} = ! $section->{$column};
        $section->save();

        return response()->json(['success' => true, 'message' => 'Toggled successfully']);
    }
}
