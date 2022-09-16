<?php

namespace App\Http\Resources\Automation;

use App\Models\Type;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ActionCollection extends ResourceCollection
{
    public function toArray($request): Collection
    {
        return $this->collection->map(function ($action) {
            return [
                'action_id' => $action['action'],
                'action_name' => Type::find($action['action'])->name,
                'vital' => $action['vital'] ?? null,
                'value' => $action['value'] ?? null,
                'content' => $action['content'] ?? null,
                'to' => $action['to'] ?? null,
            ];
        });
    }
}
