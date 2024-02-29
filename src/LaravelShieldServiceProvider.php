<?php

namespace Webdevartisan\LaravelShield;

use Webdevartisan\LaravelShield\Services\BlockedIpStoreDatabase;
use Illuminate\Support\ServiceProvider;

class LaravelShieldServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('shield.php'),
                __DIR__ . '/../config/ip.php' => config_path('ip.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'shield');
        $this->mergeConfigFrom(__DIR__ . '/../config/ip.php', 'ip');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-shield', function () {
            return new LaravelShield();
        });


        $blockedIpStoreClass = config('shield.storage_implementation_class');
        $this->app->singleton('blockedipstore', function () use ($blockedIpStoreClass) {
            return new $blockedIpStoreClass();
        });
    }
}
