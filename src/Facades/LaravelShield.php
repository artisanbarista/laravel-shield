<?php

namespace Artisanbarista\LaravelShield\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Artisanbarista\LaravelShield\LaravelShield
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
