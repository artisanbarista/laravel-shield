<?php

namespace Webdevartisan\LaravelBlocker;

use Webdevartisan\LaravelBlocker\Services\BlockedIpStoreDatabase;
use Illuminate\Support\ServiceProvider;

class LaravelBlockerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('laravel-shield.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel-shield');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-shield', function () {
            return new LaravelBlocker();
        });


        $blockedIpStoreClass = config('laravel-shield.storage_implementation_class');
        $this->app->singleton('blockedipstore', function () use ($blockedIpStoreClass) {
            return new $blockedIpStoreClass();
        });
    }
}
