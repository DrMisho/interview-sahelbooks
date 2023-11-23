<?php

namespace App\Providers;

use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        $this->app->bind('ProductService', function () {
            return new ProductService();
        });

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
