<?php

namespace App\Providers;

use App\Models\RecipeItem;
use App\Models\User;
use App\Observers\RecipeItemObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

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

        if (!app()->runningUnitTests()) {
            $this->ensureSystemUser();
        }

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }

    private function ensureSystemUser(): void
    {
        try {
            User::firstOrCreate(
                ['email' => User::SYSTEM_EMAIL],
                [
                    'name' => 'System User',
                    'password' => 'PLKJHGFDSA',
                    'locale' => 'ro',
                    'email_verified_at' => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to ensure system user.', [
                'email' => User::SYSTEM_EMAIL,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
