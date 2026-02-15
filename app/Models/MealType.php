<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'default_sort_order',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Tipuri de mese default
     */
    public const DEFAULTS = [
        ['name' => 'Mic dejun', 'default_sort_order' => 1],
        ['name' => 'Gustare AM', 'default_sort_order' => 2],
        ['name' => 'Pranz', 'default_sort_order' => 3],
        ['name' => 'Gustare PM', 'default_sort_order' => 4],
        ['name' => 'Cina', 'default_sort_order' => 5],
        ['name' => 'Gustare seara', 'default_sort_order' => 6],
    ];

    public function menuMeals(): HasMany
    {
        return $this->hasMany(MenuMeal::class);
    }

    /**
     * Scope pentru tipuri active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pentru tipuri default
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope ordonat după sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('default_sort_order');
    }
}
