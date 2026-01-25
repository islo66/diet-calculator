<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition(): array
    {
        return [
            'patient_id' => null,
            'name' => $this->faker->unique()->words(2, true),
            'yield_qty' => 0,
            'yield_unit' => 'g',
        ];
    }

    public function forPatient(Patient $patient): static
    {
        return $this->state(fn() => [
            'patient_id' => $patient->id,
        ]);
    }
}