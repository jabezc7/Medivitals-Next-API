<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Type;

class DataTypeSeeder extends Seeder
{
    public function run()
    {

        $data = [
            // Permission & Group Types
            [
                'name' => 'location',
                'group' => 'data-types',
                'locked' => true,
            ],
            [
                'name' => 'blood-sugar',
                'group' => 'data-types',
                'locked' => true,
            ],
            // Simple Options
            [
                'name' => 'oxygen-saturation',
                'group' => 'data-types',
                'locked' => true
            ],
            [
                'name' => 'temperature',
                'group' => 'data-types',
                'locked' => true
            ],
            [
                'name' => 'heart-rate',
                'group' => 'data-types',
                'locked' => true
            ],
            [
                'name' => 'blood-pressure',
                'group' => 'data-types',
                'locked' => true
            ],
        ];

        foreach ($data as $type) {
            Type::query()->create($type);
        }
    }
}
