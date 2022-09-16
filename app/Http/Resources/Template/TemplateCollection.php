<?php

namespace App\Http\Resources\Template;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class TemplateCollection extends ResourceCollection
{
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($template) {
            return [
                'id' => $template->id,
                'type' => $template->type,
                'name' => $template->name,
                'active' => $template->active,
                'quick_link' => $template->quick_link,
                'content' => $template->content
            ];
        });
    }
}
