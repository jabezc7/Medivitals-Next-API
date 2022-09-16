<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionsSeeder extends Seeder
{
    public function run()
    {
        $l1_ordering = 0;

        Section::create([
            'parent_id' => null,
            'name' => 'Dashboard',
            'icon' => 'home-lg',
            'route' => '/dashboard',
            'ordering' => $l1_ordering++,
            'level' => 1,
            'permission' => 'dashboard.index',
        ]);

        Section::create([
            'parent_id' => null,
            'name' => 'Patients',
            'icon' => 'inventory',
            'route' => '/patients',
            'ordering' => $l1_ordering++,
            'level' => 1,
            'permission' => 'patients.index',
        ]);

        Section::create([
            'parent_id' => null,
            'name' => 'Devices',
            'icon' => 'window-restore',
            'route' => '/devices',
            'ordering' => $l1_ordering++,
            'level' => 1,
            'permission' => 'devices.index',
        ]);

        Section::create([
            'parent_id' => null,
            'name' => 'Automations',
            'icon' => 'chart-network',
            'route' => '/automations',
            'ordering' => $l1_ordering++,
            'level' => 1,
            'permission' => 'automations.index',
        ]);

        $configuration = Section::create([
            'parent_id' => null,
            'name' => 'Configuration',
            'icon' => 'cog',
            'route' => null,
            'ordering' => $l1_ordering++,
            'level' => 1,
        ]);

        // Reset Child Ordering
        $l2_ordering = 0;

        Section::create([
            'parent_id' => $configuration->id,
            'name' => 'Users',
            'icon' => 'users',
            'route' => '/configuration/users',
            'ordering' => $l2_ordering++,
            'level' => 2,
            'permission' => 'users.index',
        ]);

        Section::create([
            'parent_id' => $configuration->id,
            'name' => 'Groups',
            'icon' => 'id-card',
            'route' => '/configuration/groups',
            'ordering' => $l2_ordering++,
            'level' => 2,
            'permission' => 'groups.index',
        ]);

        Section::create([
            'parent_id' => $configuration->id,
            'name' => 'Permissions',
            'icon' => 'key',
            'route' => '/configuration/permissions',
            'ordering' => $l2_ordering++,
            'level' => 2,
            'permission' => 'permissions.index',
        ]);

        Section::create([
            'parent_id' => $configuration->id,
            'name' => 'Sections',
            'icon' => 'window-restore',
            'route' => '/configuration/sections',
            'ordering' => $l2_ordering++,
            'level' => 2,
            'permission' => 'sections.index',
        ]);

        Section::create([
            'parent_id' => $configuration->id,
            'name' => 'Types',
            'icon' => 'cabinet-filing',
            'route' => '/configuration/types',
            'ordering' => $l2_ordering++,
            'level' => 2,
            'permission' => 'types.index',
        ]);

        Section::create([
            'parent_id' => $configuration->id,
            'name' => 'Templates',
            'icon' => 'cabinet-filing',
            'route' => '/configuration/template',
            'ordering' => $l2_ordering++,
            'level' => 2,
            'permission' => 'templates.index',
        ]);
    }
}
