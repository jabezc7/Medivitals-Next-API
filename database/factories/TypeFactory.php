<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'group' => Str::slug($this->faker->name . ' Group'),
            'active' => true,
            'locked' => true
        ];
    }
}
