<?php

namespace App\Providers;

use App\Domain\User\Services\PasswordHasher;
use App\Infrastructure\Eloquent\User\Services\CheckPasswordHasher;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PasswordHasher::class,
            CheckPasswordHasher::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production', 'homolog')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }
    }
}
