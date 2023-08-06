<?php

namespace Sabbirh\LaravelFileCaster;

use Illuminate\Support\ServiceProvider;
use Sabbirh\LaravelFileCaster\FileCaster;

class FileCasterServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('laravel-filecaster', function () {
            return new FileCaster();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 
    }
}
