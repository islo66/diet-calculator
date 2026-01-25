<?php

namespace App\Observers;

use App\Models\RecipeItem;

class RecipeItemObserver
{
    /**
     * Handle the RecipeItem "created" event.
     */
    public function created(RecipeItem $recipeItem): void
    {
        $recipeItem->recipe->recalculateYield();
    }

    /**
     * Handle the RecipeItem "updated" event.
     */
    public function updated(RecipeItem $recipeItem): void
    {
        $recipeItem->recipe->recalculateYield();
    }

    /**
     * Handle the RecipeItem "deleted" event.
     */
    public function deleted(RecipeItem $recipeItem): void
    {
        // Refresh recipe to avoid stale data
        if ($recipeItem->recipe) {
            $recipeItem->recipe->recalculateYield();
        }
    }
}