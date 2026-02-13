<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __('app.common.login') }} - {{ config('app.name', 'Diet Calculator') }}</title>

        <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=1">
        <meta name="app-version" content="{{ config('app.version') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
            <section class="hidden lg:flex flex-col justify-between p-12 bg-gradient-to-br from-emerald-50 via-white to-amber-50">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-600"></div>
                    <div class="text-lg font-semibold">{{ __('app.app.name') }}</div>
                </div>

                <div class="max-w-md">
                    <p class="text-3xl font-semibold leading-tight">
                        Plan meals. Track nutrients. Build healthy routines.
                    </p>
                    <p class="mt-4 text-slate-600">
                        Organize foods, recipes, and menu plans in one clean workspace.
                    </p>
                </div>

                <div class="text-sm text-slate-500">
                    <span>{{ __('app.auth.simple_fast_focused') }}</span>
                </div>
            </section>

            <section class="flex items-center justify-center p-6 lg:p-12">
                <div class="w-full max-w-md">
                    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-[0_18px_60px_-40px_rgba(15,23,42,0.6)]">
                        <div class="mb-6">
                            <h1 class="text-2xl font-semibold">{{ __('app.common.welcome_back') }}</h1>
                            <p class="mt-1 text-sm text-slate-600">{{ __('app.auth.login_to_continue') }}</p>
                        </div>

                        @if (session('status'))
                            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700">{{ __('app.auth.email') }}</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100" />
                                @error('email')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700">{{ __('app.auth.password') }}</label>
                                <input id="password" name="password" type="password" required autocomplete="current-password"
                                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-100" />
                                @error('password')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                    <input id="remember_me" name="remember" type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                                    {{ __('app.auth.remember_me') }}
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-sm text-emerald-700 hover:text-emerald-800" href="{{ route('password.request') }}">
                                        {{ __('app.auth.forgot_password') }}
                                    </a>
                                @endif
                            </div>

                            <button type="submit"
                                class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                                {{ __('app.common.log_in') }}
                            </button>
                        </form>

                        @if (Route::has('register'))
                            <p class="mt-6 text-center text-sm text-slate-600">
                                {{ __('app.auth.new_here') }}
                                <a class="font-medium text-emerald-700 hover:text-emerald-800" href="{{ route('register') }}">{{ __('app.auth.create_account') }}</a>
                            </p>
                        @endif
                    </div>

                    <p class="mt-6 text-center text-xs text-slate-500">
                        {{ __('app.auth.by_continuing') }}
                    </p>
                </div>
            </section>
        </div>
    </body>
</html>
