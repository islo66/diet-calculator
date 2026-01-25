<?php

namespace Database\Seeders;

use App\Models\MealType;
use Illuminate\Database\Seeder;

class MealTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (MealType::DEFAULTS as $default) {
            MealType::firstOrCreate(
                ['name' => $default['name']],
                [
                    'default_sort_order' => $default['default_sort_order'],
                    'is_default' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}