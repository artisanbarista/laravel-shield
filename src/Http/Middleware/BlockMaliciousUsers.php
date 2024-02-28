<?php

namespace Webdevartisan\LaravelShield\Http\Middleware;

use Webdevartisan\LaravelShield\Facades\BlockedIpStore;
use Webdevartisan\LaravelShield\Facades\LaravelShield;
use Closure;

class BlockMaliciousUsers
{
    public function handle($request, Closure $next)
    {
        if (!config('laravel-shield.protection_enabled')) {
            return $next($request);
        }

        $ip = $request->server('HTTP_CF_CONNECTING_IP') ??
              $request->server('HTTP_X_FORWARDED_FOR') ??
              $request->ip() ??
              request()->server('REMOTE_ADDR');

        if (in_array($ip, config('ip.whitelist') ?? [], true)) {
            return $next($request);
        }

        // Is this a blocked IP?
        if (BlockedIpStore::has($ip)) {
            return response('You have been blocked', 401);
        }

        // @see config/config.php
        if (LaravelShield::isMaliciousRequest()) {
            // Store blocked IP
            BlockedIpStore::create($ip);

            return response('Not accepted', 406);
        }

        return $next($request);
    }
}
