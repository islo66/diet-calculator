<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CookingProfileFactor extends Model
{
    protected $fillable = [
        'cooking_profile_id',
        'nutrient_key',
        'factor',
    ];

    protected $casts = [
        'factor' => 'decimal:4',
    ];

    public function cookingProfile(): BelongsTo
    {
        return $this->belongsTo(CookingProfile::class);
    }
}