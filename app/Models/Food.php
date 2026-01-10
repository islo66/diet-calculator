<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Food extends Model
{
    protected $table = 'foods';

    public function category(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class, 'category_id');
    }

    public function nutrients(): HasMany
    {
        return $this->hasMany(FoodNutrient::class, 'food_id');
    }

    public function nutrient(): HasOne
    {
        return $this->hasOne(FoodNutrient::class, 'food_id')
            ->where('basis_qty', 100)
            ->where('basis_unit', 'g');
    }
}
