<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Detrás del proxy/CDN del hosting el esquema puede detectarse como http;
        // en producción se fuerza https para URLs, assets de Vite y redirecciones.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
