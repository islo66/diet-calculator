<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'name',
        'yield_qty',
        'yield_unit',
        'notes',
    ];

    protected $casts = [
        'yield_qty' => 'decimal:3',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    /**
     * Calculează nutrienții totali ai rețetei (pentru toată cantitatea yield_qty)
     */
    public function calculateTotalNutrients(): array
    {
        $totals = [
            'kcal' => 0,
            'protein_g' => 0,
            'fat_g' => 0,
            'carb_g' => 0,
            'fiber_g' => 0,
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

    /**
     * Calculează nutrienții pentru o anumită porție din rețetă
     */
    public function calculateNutrientsForPortion(float $portionQty, string $portionUnit = null): array
    {
        $totalNutrients = $this->calculateTotalNutrients();

        // Dacă unitățile sunt diferite, ar trebui conversie (simplificat: presupunem aceeași unitate)
        $multiplier = $portionQty / $this->yield_qty;

        $result = [];
        foreach ($totalNutrients as $key => $value) {
            $result[$key] = $value * $multiplier;
        }

        return $result;
    }

    /**
     * Nutrienți per 100g/ml (pentru afișare standard)
     */
    public function getNutrientsPer100Attribute(): array
    {
        return $this->calculateNutrientsForPortion(100);
    }

    /**
     * Calculează greutatea totală a rețetei din ingrediente
     * Sumează toate cantitățile ingredientelor (presupunând aceeași unitate)
     */
    public function calculateTotalWeight(): float
    {
        return $this->items()->sum('qty');
    }

    /**
     * Recalculează și actualizează yield_qty din ingrediente
     */
    public function recalculateYield(): void
    {
        $totalWeight = $this->calculateTotalWeight();

        if ($totalWeight > 0) {
            $this->update(['yield_qty' => $totalWeight]);
        }
    }
}