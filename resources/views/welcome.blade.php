<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Diet Calculator te ajuta sa calculezi corect dieta: planuri de meniu, retete, alimente si nutrienti intr-un singur loc. Optimizeaza aportul nutritional si construieste obiceiuri sanatoase.">
        <meta name="keywords" content="diet calculator, plan alimentar, calcul nutritie, meniu saptamanal, retete, alimente, nutrienti, aport caloric, sanatate">
        <meta property="og:title" content="Diet Calculator - Calculeaza Corect Dieta">
        <meta property="og:description" content="Planuri de meniu, retete, alimente si nutrienti intr-un singur loc. Calculeaza corect dieta si optimizeaza aportul nutritional.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:image" content="{{ url('/favicon.svg') }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Diet Calculator - Calculeaza Corect Dieta">
        <meta name="twitter:description" content="Planuri de meniu, retete, alimente si nutrienti intr-un singur loc. Calculeaza corect dieta si optimizeaza aportul nutritional.">
        <meta name="twitter:image" content="{{ url('/favicon.svg') }}">

        <title>{{ __('app.titles.home') }} - {{ config('app.name', 'Diet Calculator') }}</title>

        <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=1">
        <meta name="app-version" content="{{ config('app.version') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <main class="min-h-screen">
            <section class="mx-auto flex min-h-screen w-full max-w-6xl flex-col justify-center px-6 py-16 lg:flex-row lg:items-center lg:gap-16">
                <div class="max-w-xl">
                    <div class="flex items-center gap-3">
                        <img src="/favicon.svg" alt="Diet Calculator logo" class="h-14 w-14 rounded-2xl">
                        <span class="text-base font-semibold text-slate-800">{{ __('app.app.name') }}</span>
                    </div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600">{{ __('app.landing.tagline') }}</p>
                    <h1 class="mt-4 text-4xl font-semibold leading-tight md:text-5xl">
                        {{ __('app.landing.headline') }}
                    </h1>
                    <p class="mt-4 text-lg text-slate-600">
                        {{ __('app.landing.subheadline') }}
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                            {{ __('app.landing.cta_login') }}
                        </a>
                        <span class="text-sm text-slate-500">{{ __('app.common.access_restricted') }}</span>
                    </div>
                </div>

                <div class="mt-12 w-full max-w-lg rounded-3xl border border-emerald-100 bg-white p-8 shadow-[0_24px_60px_-40px_rgba(15,23,42,0.7)] lg:mt-0">
                    <div class="grid gap-6">
                        <div class="flex items-start gap-4">
                            <div class="mt-1 h-10 w-10 rounded-2xl bg-emerald-100"></div>
                            <div>
                                <h3 class="text-base font-semibold">{{ __('app.landing.feature_foods_title') }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ __('app.landing.feature_foods_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="mt-1 h-10 w-10 rounded-2xl bg-amber-100"></div>
                            <div>
                                <h3 class="text-base font-semibold">{{ __('app.landing.feature_recipes_title') }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ __('app.landing.feature_recipes_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="mt-1 h-10 w-10 rounded-2xl bg-sky-100"></div>
                            <div>
                                <h3 class="text-base font-semibold">{{ __('app.landing.feature_menu_title') }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ __('app.landing.feature_menu_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
