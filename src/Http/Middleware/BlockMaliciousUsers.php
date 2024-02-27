<?php

namespace Webdevartisan\LaravelBlocker\Http\Middleware;

use Webdevartisan\LaravelBlocker\Facades\BlockedIpStore;
use Webdevartisan\LaravelBlocker\Facades\LaravelBlocker;
use Closure;

class BlockMaliciousUsers
{
    public function handle($request, Closure $next)
    {
        if (!config('laravel-shield.protection_enabled')) {
            return $next($request);
        }

        $requestIp = $request->server('HTTP_CF_CONNECTING_IP') ?? $request->ip();

        if (in_array($requestIp, config('laravel-shield.ip_whitelist'), true)) {
            return $next($request);
        }

        // Is this a blocked IP?
        if (BlockedIpStore::has($requestIp)) {
            return response('You have been blocked', 401);
        }

        // @see config/config.php
        if (LaravelBlocker::isMaliciousRequest()) {
            // Store blocked IP
            BlockedIpStore::create($requestIp);

            return response('Not accepted', 406);
        }

        return $next($request);
    }
}
