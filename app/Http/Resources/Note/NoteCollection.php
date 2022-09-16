<?php

namespace App\Http\Resources\Note;

use App\Http\Resources\User\UserSimpleResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class NoteCollection extends ResourceCollection
{
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($note) {
            $return = [
                'id' => $note->id,
                'note' => $note->note,
                'private' => $note->private,
                'noteable' => [
                    'id' => $note->noteable_id,
                    'type' => str_replace('App\\Models\\', '', $note->noteable_type)
                ],
                'meta' => [
                    'created_at' => $note->created_at,
                    'created_at_human' => $note->created_at->diffForHumans(),
                    'created_by' => new UserSimpleResource($note->creator),
                    'updated_at' => $note->updated_at,
                    'updated_at_human' => $note->updated_at->diffForHumans(),
                ],
            ];

            if ($note->replies) {
                foreach ($note->replies as $reply) {
                    $return['replies'][] = new NoteResource($reply);
                }
            }

            return $return;

        });
    }
}
