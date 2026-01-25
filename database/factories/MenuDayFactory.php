<?php

namespace Database\Factories;

use App\Models\MenuDay;
use App\Models\MenuPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuDayFactory extends Factory
{
    protected $model = MenuDay::class;

    public function definition(): array
    {
        return [
            'menu_plan_id' => MenuPlan::factory(),
            'name' => $this->faker->randomElement(['Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sambata', 'Duminica']),
        ];
    }
}