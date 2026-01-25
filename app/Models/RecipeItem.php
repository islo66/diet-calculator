<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'recipe_id',
        'food_id',
        'qty',
        'unit',
        'cooking_profile_id',
        'notes',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    public function cookingProfile(): BelongsTo
    {
        return $this->belongsTo(CookingProfile::class);
    }

    /**
     * Calculează nutrienții pentru acest ingredient (bazat pe cantitate)
     */
    public function calculateNutrients(): array
    {
        $defaults = [
            'kcal' => 0,
            'protein_g' => 0,
            'fat_g' => 0,
            'carb_g' => 0,
            'fiber_g' => 0,
            'sodium_mg' => 0,
            'potassium_mg' => 0,
            'phosphorus_mg' => 0,
        ];

        if (!$this->food) {
            return $defaults;
        }

        // Caută nutrienții pentru unitatea specificată sau default (100g)
        $nutrient = $this->food->nutrients()
            ->where('basis_unit', $this->unit)
            ->first();

        if (!$nutrient) {
            $nutrient = $this->food->nutrient; // HasOne cu 100g
        }

        if (!$nutrient) {
            return $defaults;
        }

        $multiplier = $this->qty / $nutrient->basis_qty;

        $result = [
            'kcal' => ($nutrient->kcal ?? 0) * $multiplier,
            'protein_g' => ($nutrient->protein_g ?? 0) * $multiplier,
            'fat_g' => ($nutrient->fat_g ?? 0) * $multiplier,
            'carb_g' => ($nutrient->carb_g ?? 0) * $multiplier,
            'fiber_g' => ($nutrient->fiber_g ?? 0) * $multiplier,
            'sodium_mg' => ($nutrient->sodium_mg ?? 0) * $multiplier,
            'potassium_mg' => ($nutrient->potassium_mg ?? 0) * $multiplier,
            'phosphorus_mg' => ($nutrient->phosphorus_mg ?? 0) * $multiplier,
        ];

        // Aplică factorul de gătire dacă există
        if ($this->cooking_profile_id && $this->cookingProfile) {
            $result = $this->applyCookingFactors($result);
        }

        return $result;
    }

    /**
     * Aplică factorii de gătire asupra nutrienților
     */
    private function applyCookingFactors(array $nutrients): array
    {
        $factors = $this->cookingProfile->factors()
            ->pluck('factor', 'nutrient_key')
            ->toArray();

        foreach ($factors as $key => $factor) {
            if (isset($nutrients[$key])) {
                $nutrients[$key] *= $factor;
            }
        }

        return $nutrients;
    }
}