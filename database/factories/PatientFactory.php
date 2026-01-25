<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'sex' => $this->faker->randomElement(['M', 'F']),
            'birthdate' => $this->faker->date(),
            'current_height_cm' => $this->faker->numberBetween(150, 190),
            'current_weight_kg' => $this->faker->numberBetween(50, 100),
            'is_active' => true,
        ];
    }
}