<?php

namespace Webdevartisan\LaravelBlocker\Http\Middleware;

use Webdevartisan\LaravelBlocker\Facades\BlockedIpStore;
use Webdevartisan\LaravelBlocker\Facades\LaravelBlocker;
use Closure;

class BlockMaliciousUsers
{
    public function handle($request, Closure $next)
    {
        $protectionEnabled = config('laravel-shield.protection_enabled');

        if (!$protectionEnabled) {
            return $next($request);
        }

        $requestIp = request()->ip();

        // Is this a blocked IP?
        if (BlockedIpStore::has($requestIp)) {
            return response('You have been blocked', 401);
        }

        // @see config/config.php
        if (LaravelBlocker::isMaliciousRequest() || LaravelBlocker::isMaliciousUserAgent() || LaravelBlocker::isMaliciousPattern()) {
            // Store blocked IP
            BlockedIpStore::create($requestIp);

            return response('Not accepted', 406);
        }

        return $next($request);
    }
}
