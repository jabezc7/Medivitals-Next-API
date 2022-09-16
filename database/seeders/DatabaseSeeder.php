<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(SectionsSeeder::class);
        $this->call(TypesSeeder::class);
        Artisan::call('permissions:update');
        $this->call(InitialSeeder::class);
        $this->call(DemoSeeder::class);
    }
}
