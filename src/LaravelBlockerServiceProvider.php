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
        /*
         * Optional methods to load your package assets
         */
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-shield');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-shield');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('laravel-shield.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-shield'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-shield'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-shield'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
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
