<?php

namespace Webdevartisan\LaravelShield\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Webdevartisan\LaravelShield\LaravelShield
 */
class LaravelShield extends Facade
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
