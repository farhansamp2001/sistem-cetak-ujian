<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS di environment Vercel / Production
        if (env('APP_ENV') !== 'local' || isset($_SERVER['VERCEL_URL'])) {
            URL::forceScheme('https');
        }
    }
}
