<?php

use App\Http\Controllers\FoodNutrientController;
use App\Http\Controllers\MealItemController;
use App\Http\Controllers\MealTypeController;
use App\Http\Controllers\MenuDayController;
use App\Http\Controllers\MenuPlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/foods', [FoodController::class, 'index'])->name('foods.index');
    Route::resource('nutrients', FoodNutrientController::class)->except(['show']);

    // Menu Plans
    Route::resource('menu-plans', MenuPlanController::class);

    // Menu Days (nested pentru create/store)
    Route::get('menu-plans/{menuPlan}/days/create', [MenuDayController::class, 'create'])->name('menu-days.create');
    Route::post('menu-plans/{menuPlan}/days', [MenuDayController::class, 'store'])->name('menu-days.store');
    Route::get('menu-days/{menuDay}/edit', [MenuDayController::class, 'edit'])->name('menu-days.edit');
    Route::put('menu-days/{menuDay}', [MenuDayController::class, 'update'])->name('menu-days.update');
    Route::delete('menu-days/{menuDay}', [MenuDayController::class, 'destroy'])->name('menu-days.destroy');

    // Meal Items (nested pentru create/store)
    Route::get('menu-meals/{menuMeal}/items/create', [MealItemController::class, 'create'])->name('meal-items.create');
    Route::post('menu-meals/{menuMeal}/items', [MealItemController::class, 'store'])->name('meal-items.store');
    Route::get('meal-items/{mealItem}/edit', [MealItemController::class, 'edit'])->name('meal-items.edit');
    Route::put('meal-items/{mealItem}', [MealItemController::class, 'update'])->name('meal-items.update');
    Route::delete('meal-items/{mealItem}', [MealItemController::class, 'destroy'])->name('meal-items.destroy');

    // Meal Types (tipuri de mese)
    Route::resource('meal-types', MealTypeController::class)->except(['show']);

    // Recipes
    Route::resource('recipes', RecipeController::class);

    // Recipe Items (ingrediente)
    Route::get('recipes/{recipe}/items/create', [RecipeItemController::class, 'create'])->name('recipe-items.create');
    Route::post('recipes/{recipe}/items', [RecipeItemController::class, 'store'])->name('recipe-items.store');
    Route::get('recipe-items/{recipeItem}/edit', [RecipeItemController::class, 'edit'])->name('recipe-items.edit');
    Route::put('recipe-items/{recipeItem}', [RecipeItemController::class, 'update'])->name('recipe-items.update');
    Route::delete('recipe-items/{recipeItem}', [RecipeItemController::class, 'destroy'])->name('recipe-items.destroy');
});

require __DIR__ . '/auth.php';