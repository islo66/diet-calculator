<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class MenuDay extends Model
{
    use HasFactory;
    protected $fillable = [
        'menu_plan_id',
        'name',
        'notes',
    ];

    public function menuPlan(): BelongsTo
    {
        return $this->belongsTo(MenuPlan::class);
    }

    public function meals(): HasMany
    {
        return $this->hasMany(MenuMeal::class)->orderBy('sort_order');
    }

    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(MealItem::class, MenuMeal::class);
    }

    public function calculateNutrients(): array
    {
        $totals = [
            'kcal' => 0,
            'protein_g' => 0,
            'fat_g' => 0,
            'carb_g' => 0,
            'sodium_mg' => 0,
            'potassium_mg' => 0,
            'phosphorus_mg' => 0,
        ];

        foreach ($this->meals as $meal) {
            $mealNutrients = $meal->calculateNutrients();
            foreach ($totals as $key => $value) {
                $totals[$key] += $mealNutrients[$key];
            }
        }

        return $totals;
    }
}