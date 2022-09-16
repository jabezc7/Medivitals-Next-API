<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $systemType = Type::where('group', 'group-types')->where('name', 'System')->first();

        // Create Groups
        $superAdminGroup = Group::create([
            'name' => 'Super Administrator',
            'description' => 'Highest Level Access',
            'ordering' => 1,
            'type_id' => $systemType->id,
        ]);

        Group::create([
            'name' => 'Administrator',
            'description' => 'Administrator Level Access',
            'ordering' => 2,
            'type_id' => $systemType->id,
        ]);

        Group::create([
            'name' => 'Stakeholder',
            'description' => 'Stakeholder Level Access',
            'ordering' => 3,
            'type_id' => $systemType->id,
        ]);

        Group::create([
            'name' => 'Patient',
            'description' => 'Patient Access',
            'ordering' => 4,
            'type_id' => $systemType->id,
        ]);

        // Create Super Admin Users
        $ryanAdmin = User::create([
            'first' => 'Ryan',
            'last' => 'Bown',
            'email' => 'ryan@niftee.com.au',
            'position' => 'Software Developer',
            'password' => 'password',
            'super_admin' => true,
            'mobile' => ''
        ]);

        $ryanAdmin->groups()->attach($superAdminGroup);

        // Create Super Admin Users
        $paulAdmin = User::create([
            'first' => 'Paul',
            'last' => 'Fildes',
            'email' => 'paul@niftee.com.au',
            'position' => 'Software Developer',
            'password' => 'password',
            'super_admin' => true,
            'mobile' => ''
        ]);

        $paulAdmin->groups()->attach($superAdminGroup);

        $davidAdmin = User::create([
            'first' => 'David',
            'last' => 'Wolfinger',
            'email' => 'david@mozzee.com',
            'position' => 'Project Manager',
            'password' => 'password',
            'super_admin' => true,
            'mobile' => '',
        ]);

        $davidAdmin->groups()->attach($superAdminGroup);
    }
}
