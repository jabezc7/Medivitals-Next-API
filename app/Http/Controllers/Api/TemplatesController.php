<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\TemplateFilter;
use App\Http\Resources\Template\TemplateCollection;
use App\Http\Resources\Template\TemplateResource;
use App\Models\Template;
use App\Models\Type;
use Illuminate\Database\Eloquent\Model;
use App\Policies\TemplatePolicy;
use Illuminate\Http\JsonResponse;

class TemplatesController extends Controller
{
    use CrudTrait;

    protected string|Model $model = Template::class;

    protected string $policy = TemplatePolicy::class;

    protected string $collection = TemplateCollection::class;

    protected string $resource = TemplateResource::class;

    protected string $filter = TemplateFilter::class;

    public function getSmsTemplates(): JsonResponse
    {
        $typeId = Type::getTypeIDBySlug('template-types-sms');
        $templates = Template::query()
            ->where('type_id', $typeId)
            ->where('quick_link', true)
            ->get();

        return response()->json(new $this->collection($templates));
    }
}
