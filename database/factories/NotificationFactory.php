<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::query()->inRandomOrder()->first()->id,
            'message' => $this->faker->sentence,
            'created_at' => $this->faker->dateTimeBetween('-30 days')
        ];
    }

    public function alert(): NotificationFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'alert' => true,
                'priority' => $this->faker->randomElement(['Normal', 'Medium', 'High']),
                'triggers' => $this->triggers()[$this->faker->randomElement(['temperature', 'heart_rate', 'blood_pressure'])]
            ];
        });
    }

    private function triggers(): array
    {
        return [
            'temperature' => [[
                'vital' => Type::query()->where('name', 'Temperature')->where('group', 'vital-types')->first()->slug,
                'operator' => '>',
                'value' => (string)$this->faker->numberBetween(35, 41),
                'comparison' => [
                    'period' => 'reading',
                    'value' => '2'
                ]
            ]],
            'heart_rate' => [[
                'vital' => Type::query()->where('name', 'Heart Rate')->where('group', 'vital-types')->first()->slug,
                'operator' => '>',
                'value' => (string)$this->faker->numberBetween(125, 180),
                'comparison' => [
                    'period' => 'reading',
                    'value' => '2'
                ]
            ]],
            'blood_pressure' => [
                [
                    'vital' => Type::query()->where('name', 'Blood Pressure (Systolic)')->where('group', 'vital-types')->first()->slug,
                    'operator' => '>',
                    'value' => (string)$this->faker->numberBetween(120, 180),
                    'comparison' => [
                        'period' => 'reading',
                        'value' => '2'
                    ]
                ],
                [
                    'vital' => Type::query()->where('name', 'Blood Pressure (Diastolic)')->where('group', 'vital-types')->first()->slug,
                    'operator' => '>',
                    'value' => (string)$this->faker->numberBetween(80, 120),
                    'comparison' => [
                        'period' => 'reading',
                        'value' => '2'
                    ]
                ]
            ]
        ];
    }
}
