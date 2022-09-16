<?php

namespace App\Http\Resources\Attachment;

use App\Http\Resources\User\UserSimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray($request): array
    {
        $filename = array_reverse(explode('/', $this->path));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
            'filename' => $filename[0],
            'meta' => $this->meta,
            'group' => $this->group,
            'mime' => $this->mime,
            'folder' => $this->folder,
            'size' => $this->size ? $this->human_filesize((int) $this->size, 0) : null,
            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at->diffForHumans(),
            'created_by' => $this->creator ? new UserSimpleResource($this->creator) : null,
        ];
    }

    public function human_filesize($size, $precision = 2): string
    {
        for ($i = 0; ($size / 1024) > 0.9; $i++, $size /= 1024) {}

        return round($size, $precision).' '.['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'][$i];
    }
}
