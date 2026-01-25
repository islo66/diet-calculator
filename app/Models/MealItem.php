<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealItem extends Model
{
    protected $fillable = [
        'menu_meal_id',
        'item_type',
        'food_id',
        'recipe_id',
        'portion_qty',
        'portion_unit',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'portion_qty' => 'decimal:3',
    ];

    public const TYPE_FOOD = 'food';
    public const TYPE_RECIPE = 'recipe';

    public function menuMeal(): BelongsTo
    {
        return $this->belongsTo(MenuMeal::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Calculează nutrienții pentru acest item bazat pe porție
     */
    public function calculateNutrients(): array
    {
        $defaults = [
            'kcal' => 0,
            'protein_g' => 0,
            'fat_g' => 0,
            'carb_g' => 0,
            'sodium_mg' => 0,
            'potassium_mg' => 0,
            'phosphorus_mg' => 0,
        ];

        // Pentru rețete
        if ($this->item_type === self::TYPE_RECIPE && $this->recipe) {
            $this->recipe->loadMissing('items.food.nutrients');
            return $this->recipe->calculateNutrientsForPortion($this->portion_qty, $this->portion_unit);
        }

        // Pentru alimente individuale (fallback)
        if ($this->item_type === self::TYPE_FOOD && $this->food) {
            $nutrient = $this->food->nutrients()
                ->where('basis_unit', $this->portion_unit)
                ->first();

            if (!$nutrient) {
                $nutrient = $this->food->nutrient;
            }

            if ($nutrient) {
                $multiplier = $this->portion_qty / $nutrient->basis_qty;

                return [
                    'kcal' => ($nutrient->kcal ?? 0) * $multiplier,
                    'protein_g' => ($nutrient->protein_g ?? 0) * $multiplier,
                    'fat_g' => ($nutrient->fat_g ?? 0) * $multiplier,
                    'carb_g' => ($nutrient->carb_g ?? 0) * $multiplier,
                    'sodium_mg' => ($nutrient->sodium_mg ?? 0) * $multiplier,
                    'potassium_mg' => ($nutrient->potassium_mg ?? 0) * $multiplier,
                    'phosphorus_mg' => ($nutrient->phosphorus_mg ?? 0) * $multiplier,
                ];
            }
        }

        return $defaults;
    }

    /**
     * Accessor pentru numele itemului (food sau recipe)
     */
    public function getNameAttribute(): string
    {
        if ($this->item_type === self::TYPE_RECIPE) {
            return $this->recipe?->name ?? 'Unknown Recipe';
        }

        return $this->food?->name ?? 'Unknown Food';
    }
}