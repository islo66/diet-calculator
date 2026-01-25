<?php

namespace Database\Factories;

use App\Models\MenuPlan;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuPlanFactory extends Factory
{
    protected $model = MenuPlan::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'name' => $this->faker->words(3, true),
            'starts_at' => now(),
            'ends_at' => now()->addDays(7),
            'is_active' => true,
        ];
    }
}