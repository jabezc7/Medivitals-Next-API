<?php

namespace App\Http\Resources\Note;

use App\Http\Resources\User\UserSimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray($request) : array
    {
        return [
            'id' => $this->id,
            'note' => $this->note,
            'parent_id' => $this->parent_id,
            'private' => $this->private,
            'meta' => [
                'created_at' => $this->created_at,
                'created_at_human' => $this->created_at->diffForHumans(),
                'created_by' => new UserSimpleResource($this->creator),
                'updated_at' => $this->updated_at,
                'updated_at_human' => $this->updated_at->diffForHumans(),
            ],
        ];
    }
}
