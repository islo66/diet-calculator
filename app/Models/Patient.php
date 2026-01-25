<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'sex',
        'birthdate',
        'current_height_cm',
        'current_weight_kg',
        'diagnosis',
        'target_kcal_per_day',
        'target_protein_g_per_day',
        'target_carbs_g_per_day',
        'target_fat_g_per_day',
        'limit_sodium_mg_per_day',
        'limit_potassium_mg_per_day',
        'limit_phosphorus_mg_per_day',
        'limit_fluids_ml_per_day',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'is_active' => 'boolean',
        'current_weight_kg' => 'decimal:2',
        'target_protein_g_per_day' => 'decimal:2',
        'target_carbs_g_per_day' => 'decimal:2',
        'target_fat_g_per_day' => 'decimal:2',
    ];

    public function menuPlans(): HasMany
    {
        return $this->hasMany(MenuPlan::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}