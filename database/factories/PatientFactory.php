<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first' => $this->faker->firstName,
            'last' => $this->faker->lastName,
            'email' => $this->faker->email,
            'mobile' => '0' . $this->faker->numberBetween(400000000, 499999999),
            'phone' => $this->faker->numberBetween(90000000, 99999999),
            'active' => true,
            'address_1' => $this->faker->streetAddress,
            'suburb' => $this->faker->city,
            'postcode' => $this->faker->numberBetween(6000, 6999),
            'state' => 'WA',
            'country' => 'Australia',
            'medicare_number' => $this->faker->numberBetween(1000000000, 1999999999),
            'medicare_expiry' => $this->faker->numberBetween(1,12) . '/' . $this->faker->numberBetween(25,30),
            'medicare_position' => '1',
            'private_health_fund' => 'HBF',
            'private_health_membership_no' => $this->faker->numberBetween(100000000, 199999999),
            'gp_medical_centre' => 'Springfield Medical',
            'gp_name' => 'Dr Nick',
            'gp_phone' => '9123 1234',
            'gp_email' => 'dr.nick@medivitals.app'
        ];
    }
}
