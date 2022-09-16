<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Type;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class UpdatePermissions extends Command
{
    protected $signature = 'permissions:update';

    protected $description = 'Update permissions from routes';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $routeCollection = Route::getRoutes();
        $checkArray = [];
        $systemType = Type::where('group', 'permission-types')->where('name', 'System')->first();

        foreach ($routeCollection as $value) {
            if ($value->getName()) {
                $checkArray[] = $value->getName();

                if (! Permission::where('route', $value->getName())->first()) {
                    $sp = explode('.', $value->getName());
                    $name = implode(' > ', $sp);

                    $description = '';

                    if (in_array('index', $sp)) {
                        $description = 'Allow User to view or retrieve all '.$sp[0];
                    } elseif (in_array('create', $sp)) {
                        if (substr($sp[0], -3, 3) == 'ies') {
                            $sp[0] = substr($sp[0], 0, -3).'y';
                        } elseif (substr($sp[0], -1) == 's') {
                            $sp[0] = substr($sp[0], 0, -1);
                        }
                        $description = 'Allow User to view the create new '.$sp[0].' form';
                    } elseif (in_array('destroy', $sp)) {
                        $description = 'Allow User to delete '.$sp[0];
                    } elseif (in_array('edit', $sp)) {
                        if (substr($sp[0], -3, 3) == 'ies') {
                            $sp[0] = substr($sp[0], 0, -3).'y';
                        } elseif (substr($sp[0], -1) == 's') {
                            $sp[0] = substr($sp[0], 0, -1);
                        }
                        $description = 'Allow User to view the edit '.$sp[0].' form';
                    } elseif (in_array('show', $sp)) {
                        if (substr($sp[0], -3, 3) == 'ies') {
                            $sp[0] = substr($sp[0], 0, -3).'y';
                        } elseif (substr($sp[0], -1) == 's') {
                            $sp[0] = substr($sp[0], 0, -1);
                        }
                        $description = 'Allow User to view or retrieve a single '.$sp[0];
                    } elseif (in_array('store', $sp)) {
                        if (substr($sp[0], -3, 3) == 'ies') {
                            $sp[0] = substr($sp[0], 0, -3).'y';
                        } elseif (substr($sp[0], -1) == 's') {
                            $sp[0] = substr($sp[0], 0, -1);
                        }
                        $description = 'Allow User to save a new '.$sp[0];
                    } elseif (in_array('update', $sp)) {
                        if (substr($sp[0], -3, 3) == 'ies') {
                            $sp[0] = substr($sp[0], 0, -3).'y';
                        } elseif (substr($sp[0], -1) == 's') {
                            $sp[0] = substr($sp[0], 0, -1);
                        }
                        $description = 'Allow User to update an existing '.$sp[0];
                    } elseif (in_array('detach', $sp)) {
                        if (substr($sp[0], -3, 3) == 'ies') {
                            $sp[0] = substr($sp[0], 0, -3).'y';
                        } elseif (substr($sp[0], -1) == 's') {
                            $sp[0] = substr($sp[0], 0, -1);
                        }
                        $description = 'Allow User to remove relationships with '.$sp[0];
                    } elseif (in_array('attach', $sp)) {
                        if (substr($sp[0], -3, 3) == 'ies') {
                            $sp[0] = substr($sp[0], 0, -3).'y';
                        } elseif (substr($sp[0], -1) == 's') {
                            $sp[0] = substr($sp[0], 0, -1);
                        }
                        $description = 'Allow User to add relationships with '.$sp[0];
                    } elseif (in_array('form-data', $sp)) {
                        if (substr($sp[0], -3, 3) == 'ies') {
                            $sp[0] = substr($sp[0], 0, -3).'y';
                        } elseif (substr($sp[0], -1) == 's') {
                            $sp[0] = substr($sp[0], 0, -1);
                        }
                        $description = 'Allow User to retrieve form options for '.$sp[0];
                    }

                    Permission::create([
                        'name' => ucwords(str_replace('-', ' ', $name)),
                        'route' => $value->getName(),
                        'description' => $description,
                        'active' => true,
                        'type_id' => $systemType->id,
                    ]);
                }
            }
        }

        $permissions = Permission::get();

        foreach ($permissions as $permission) {
            if (! in_array($permission->route, $checkArray)) {
                Permission::find($permission->id)->delete();
            }
        }

        return null;
    }
}
