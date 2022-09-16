<?php

namespace App\Http\Resources\Attachment;

use App\Http\Resources\User\UserSimpleResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class AttachmentCollection extends ResourceCollection
{
    public function toArray($request): array|Collection|\JsonSerializable|Arrayable
    {
        return $this->collection->map(function ($attachment) {
            $filename = array_reverse(explode('/', $attachment->path));

            return [
                'id' => $attachment->id,
                'name' => $attachment->name,
                'path' => $attachment->path,
                'filename' => $filename[0],
                'meta' => $attachment->meta,
                'group' => $attachment->group,
                'mime' => $attachment->mime,
                'folder' => $attachment->folder,
                'size' => $attachment->size ? $this->human_filesize((int) $attachment->size, 0) : null,
                'edit' => false,
                'created_at' => $attachment->created_at,
                'created_at_human' => $attachment->created_at->diffForHumans(),
                'created_by' => $attachment->creator ? new UserSimpleResource($attachment->creator) : null,
            ];
        });
    }

    public function human_filesize($size, $precision = 2): string
    {
        for ($i = 0; ($size / 1024) > 0.9; $i++, $size /= 1024) {}

        return round($size, $precision).' '.['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'][$i];
    }
}
