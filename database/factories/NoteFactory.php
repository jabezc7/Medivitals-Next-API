<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'note' => $this->faker->paragraph,
            'noteable_type' => Patient::class,
            'noteable_id' => Patient::query()->inRandomOrder()->first()->id
        ];
    }
}
