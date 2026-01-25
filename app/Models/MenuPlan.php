<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class MenuPlan extends Model
{
    protected $fillable = [
        'patient_id',
        'name',
        'starts_at',
        'ends_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(MenuDay::class)->orderBy('id');
    }

    public function meals(): HasManyThrough
    {
        return $this->hasManyThrough(MenuMeal::class, MenuDay::class);
    }
}