<?php

namespace Webdevartisan\LaravelShield\Facades;

use Illuminate\Support\Facades\Facade;

class BlockedIpStore extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'blockedipstore';
    }
}
