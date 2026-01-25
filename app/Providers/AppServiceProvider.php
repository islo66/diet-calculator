<?php

namespace App\Providers;

use App\Models\RecipeItem;
use App\Observers\RecipeItemObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RecipeItem::observe(RecipeItemObserver::class);
    }
}
