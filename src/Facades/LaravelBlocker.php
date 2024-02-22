<?php

namespace Webdevartisan\LaravelBlocker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Webdevartisan\LaravelBlocker\LaravelBlocker
 */
class LaravelBlocker extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-shield';
    }
}
