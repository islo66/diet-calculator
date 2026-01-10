<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodsFromCsvSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/data/foods.csv');

        if (!file_exists($path)) {
            throw new \RuntimeException('CSV file not found: ' . $path);
        }

        $handle = fopen($path, 'r');
        fgetcsv($handle, 0, ',');

        DB::transaction(function () use ($handle) {
            $currentCategoryId = null;

            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $name = isset($row[0]) ? trim($row[0]) : '';

                if ($name === '') {
                    continue;
                }

                $hasNumericData = false;
                for ($i = 1; $i <= 7; $i++) {
                    if (isset($row[$i]) && trim($row[$i]) !== '') {
                        $hasNumericData = true;
                        break;
                    }
                }

                if (!$hasNumericData) {
                    $currentCategoryId = DB::table('food_categories')->where('name', $name)->value('id');

                    if (!$currentCategoryId) {
                        $currentCategoryId = DB::table('food_categories')->insertGetId([
                            'name' => $name,
                            'is_active' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    continue;
                }

                $existingFoodId = DB::table('foods')
                    ->where('name', $name)
                    ->where('category_id', $currentCategoryId)
                    ->value('id');

                if (!$existingFoodId) {
                    $existingFoodId = DB::table('foods')->insertGetId([
                        'category_id' => $currentCategoryId,
                        'name' => $name,
                        'default_unit' => 'g',
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('foods')->where('id', $existingFoodId)->update([
                        'default_unit' => 'g',
                        'is_active' => 1,
                        'updated_at' => now(),
                    ]);
                }

                DB::table('food_nutrients')->updateOrInsert(
                    [
                        'food_id' => $existingFoodId,
                        'basis_qty' => 100,
                        'basis_unit' => 'g',
                    ],
                    [
                        'protein_g' => $this->num($row[1] ?? null),
                        'fat_g' => $this->num($row[2] ?? null),
                        'carb_g' => $this->num($row[3] ?? null),

                        'potassium_mg' => $this->num($row[4] ?? null),
                        'phosphorus_mg' => $this->num($row[5] ?? null),
                        'sodium_mg' => $this->num($row[6] ?? null),
                        'kcal' => $this->num($row[7] ?? null),

                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        fclose($handle);
    }

    private function num($value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = str_replace(',', '.', trim((string) $value));
        return $value === '' ? null : (float) $value;
    }
}
