<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $routeName = request()->route()?->getName() ?? '';
            $routeTitles = [
                'dashboard' => 'app.titles.dashboard',
                'foods.index' => 'app.titles.foods',
                'foods.create' => 'app.titles.foods_create',
                'foods.edit' => 'app.titles.foods_edit',
                'foods.show' => 'app.titles.foods_show',
                'nutrients.index' => 'app.titles.nutrients',
                'nutrients.create' => 'app.titles.nutrients_create',
                'nutrients.edit' => 'app.titles.nutrients_edit',
                'menu-plans.index' => 'app.titles.menu_plans',
                'menu-plans.create' => 'app.titles.menu_plans_create',
                'menu-plans.edit' => 'app.titles.menu_plans_edit',
                'menu-plans.show' => 'app.titles.menu_plans_show',
                'menu-days.create' => 'app.titles.menu_days_create',
                'menu-days.edit' => 'app.titles.menu_days_edit',
                'meal-items.create' => 'app.titles.meal_items_create',
                'meal-items.edit' => 'app.titles.meal_items_edit',
                'meal-types.index' => 'app.titles.meal_types',
                'meal-types.create' => 'app.titles.meal_types_create',
                'meal-types.edit' => 'app.titles.meal_types_edit',
                'recipes.index' => 'app.titles.recipes',
                'recipes.create' => 'app.titles.recipes_create',
                'recipes.edit' => 'app.titles.recipes_edit',
                'recipes.show' => 'app.titles.recipes_show',
                'recipe-items.create' => 'app.titles.recipe_items_create',
                'recipe-items.edit' => 'app.titles.recipe_items_edit',
                'profile.edit' => 'app.titles.profile',
            ];
            $pageTitle = $title ?? $attributes->get('title') ?? __($routeTitles[$routeName] ?? 'app.titles.app');
        @endphp

        <title>{{ $pageTitle }} - {{ config('app.name', 'Diet Calculator') }}</title>

        <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=1">
        <meta name="app-version" content="{{ config('app.version') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex">
        @include('layouts.sidebar')

        <div class="flex-1">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
    </body>
</html>
