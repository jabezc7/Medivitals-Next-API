<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AutomationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => null,
            'name' => $this->faker->name . 'Automation',
            'description' => $this->faker->sentence,
            'triggers' => [],
            'actions' => [],
            'active' => true,
            'global' => false
        ];
    }
}
