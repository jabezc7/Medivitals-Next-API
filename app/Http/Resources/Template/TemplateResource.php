<?php

namespace App\Http\Resources\Template;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'id' => $this->id,
            'type_id' => $this->type_id,
            'type' => $this->type,
            'name' => $this->name,
            'view' => $this->view,
            'path' => $this->path,
            'content' => $this->content,
            'active' => $this->active,
            'quick_link' => $this->quick_link,
        ];
    }
}
