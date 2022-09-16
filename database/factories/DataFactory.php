<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataFactory extends Factory
{
    public function definition(): array
    {
        return [
            'device_id' => Device::inRandomOrder()->first()->id,
            'type' => '',
            'value' => ''
        ];
    }

    public function temperature($value = null): DataFactory
    {
        return $this->state(function (array $attributes) use ($value){
            return [
                'type' => 'temperature',
                'value' => $value ?? $this->faker->numberBetween(35, 40)
            ];
        });
    }

    public function heartRate($min = 50, $max = 200): DataFactory
    {
        return $this->state(function (array $attributes) use ($max, $min) {
            return [
                'type' => 'heart_rate',
                'value' => $this->faker->numberBetween($min, $max)
            ];
        });
    }

    public function bloodPressure($value = null): DataFactory
    {
        return $this->state(function (array $attributes) use ($value) {
            return [
                'type' => 'blood_pressure',
                'value' => $value ?? $this->faker->numberBetween(100, 180) . '/' . $this->faker->numberBetween(60, 120)
            ];
        });
    }
}
