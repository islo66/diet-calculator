<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuMeal extends Model
{
    use HasFactory;
    protected $fillable = [
        'menu_day_id',
        'name',
        'sort_order',
    ];

    public const MEAL_BREAKFAST = 'Mic dejun';
    public const MEAL_SNACK_AM = 'Gustare AM';
    public const MEAL_LUNCH = 'Pranz';
    public const MEAL_SNACK_PM = 'Gustare PM';
    public const MEAL_DINNER = 'Cina';

    public static function mealTypes(): array
    {
        return [
            self::MEAL_BREAKFAST,
            self::MEAL_SNACK_AM,
            self::MEAL_LUNCH,
            self::MEAL_SNACK_PM,
            self::MEAL_DINNER,
        ];
    }

    public function menuDay(): BelongsTo
    {
        return $this->belongsTo(MenuDay::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MealItem::class)->orderBy('sort_order');
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

        foreach ($this->items as $item) {
            $itemNutrients = $item->calculateNutrients();
            foreach ($totals as $key => $value) {
                $totals[$key] += $itemNutrients[$key];
            }
        }

        return $totals;
    }
}