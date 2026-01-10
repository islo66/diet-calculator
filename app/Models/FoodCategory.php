<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodCategory extends Model
{
    public function foods(): HasMany
    {
        return $this->hasMany(Food::class, 'category_id');
    }
}
