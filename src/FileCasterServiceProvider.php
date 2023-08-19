<?php

namespace Sabbir268\LaravelFileCaster;

use Illuminate\Support\ServiceProvider;
use Sabbir268\LaravelFileCaster\FileCaster;

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

        $this->mergeConfigFrom(
            __DIR__ . '/../config/filecaster.php',
            'filecaster'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/filecaster.php' => config_path('filecaster.php'),
        ]);
    }
}
