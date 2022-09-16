<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'imei' => $this->faker->numberBetween(1000000000, 9999999999),
            'number' => '0' . $this->faker->numberBetween(400000000, 499999999),
            'nickname' => $this->faker->name
        ];
    }
}
