<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodNutrient extends Model
{
    use HasFactory;
    protected $table = 'food_nutrients';

    protected $fillable = [
        'food_id',
        'basis_qty',
        'basis_unit',
        'kcal',
        'protein_g',
        'fat_g',
        'carb_g',
        'fiber_g',
        'sodium_mg',
        'potassium_mg',
        'phosphorus_mg',
        'calcium_mg',
        'magnesium_mg',
        'iron_mg',
    ];

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class, 'food_id');
    }
}
