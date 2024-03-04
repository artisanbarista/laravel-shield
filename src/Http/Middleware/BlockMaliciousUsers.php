<?php

namespace Webdevartisan\LaravelShield\Http\Middleware;

use Webdevartisan\LaravelShield\Facades\BlockedIpStore;
use Webdevartisan\LaravelShield\Facades\LaravelShield;
use Closure;
use Illuminate\Support\Facades\Log;

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
            return $next($request);
        }

        // Is this a blocked IP?
        if (BlockedIpStore::has($ip)) {
            if (config('shield.logging_enabled') && (BlockedIpStore::attempts($ip) === config('shield.max_attempts'))) {
                Log::info("$ip Malicious IP Blocked");
            }
            return response('You have been blocked', 401);
        }

        // @see config/config.php
        if (LaravelShield::isMaliciousRequest()) {
            if (config('shield.logging_enabled')) {
                Log::info("$ip Malicious Request Detected");
            }
            // Store blocked IP
            BlockedIpStore::create($ip);
            return response('Not accepted', 406);
        }

        return $next($request);
    }
}
