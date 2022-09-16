<?php

namespace App\Http\Filters;

use App\Http\Controllers\Api\AuthController;
use Illuminate\Database\Eloquent\Builder;

class PermissionFilter implements Filterable
{
    public function apply(Builder $builder, $filters)
    {
        // Get Users Permissions
        $controls = null;

        if (! auth()->user()->isSuperAdmin()) {
            if (! $controls = AuthController::getAccessControl()) {
                $controls['permissions'] = [];
            }
        }

        if (isset($filters->search) && $filters->search->value != '') {
            $search = $filters->search->value;
            $builder->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if (isset($filters->type)) {
            if (is_string($filters->type) && $filters->type != '') {
                $builder->where('type_id', $filters->type);
            } elseif (is_object($filters->type) && isset($filters->type->value) && $filters->type->value != '') {
                $builder->where('type_id', $filters->type->value);
            }
        }

        if (isset($controls['permissions'])){
            $builder->whereIn('id', $controls['permissions']);
        }

        if (request('standard') === 1 || request('standard') === 'true' || request('standard') === true) {
            $builder->where('route', 'like', '%.index%')
                ->orWhere('route', 'like', '%.create%')
                ->orWhere('route', 'like', '%.edit%')
                ->orWhere('route', 'like', '%.show%')
                ->orWhere('route', 'like', '%.destroy%')
                ->orWhere('route', 'like', '%.store%')
                ->orWhere('route', 'like', '%.update%');
        }
    }
}
