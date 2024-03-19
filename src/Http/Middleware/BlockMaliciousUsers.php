<?php

namespace Webdevartisan\LaravelShield\Http\Middleware;

use Webdevartisan\LaravelShield\Facades\BlockedIpStore;
use Webdevartisan\LaravelShield\Facades\LaravelShield;
use Closure;

class BlockMaliciousUsers
{
    public function handle($request, Closure $next)
    {
        if (!config('shield.protection_enabled')) {
            return $next($request);
        }

        $ip = $request->server('HTTP_CF_CONNECTING_IP') ??
              $request->server('HTTP_X_FORWARDED_FOR') ??
              $request->ip() ??
              request()->server('REMOTE_ADDR');

        if (in_array($ip, config('ip.whitelist') ?? [], true)) {
            if (LaravelShield::isMaliciousRequest()) {
                abort(401);
            }
        }

        // Is this a blocked IP?
        if (BlockedIpStore::has($ip)) {
            abort(401);
        }

        // @see config/config.php
        if (LaravelShield::isMaliciousRequest()) {

            if (LaravelShield::isValidBot($ip)) {
                return $next($request);
            }

            // Store blocked IP
            BlockedIpStore::create($ip);

            if (BlockedIpStore::attempts($ip) === config('shield.max_attempts')) {
                LaravelShield::log("$ip Malicious IP Blocked");
            }

            abort(401);
        }

        return $next($request);
    }
}
