<?php

namespace Webdevartisan\LaravelBlocker\Http\Middleware;

use Webdevartisan\LaravelBlocker\Exceptions\BlockedUserException;
use Webdevartisan\LaravelBlocker\Exceptions\MaliciousUrlException;
use Webdevartisan\LaravelBlocker\Exceptions\MaliciousUserAgentException;
use Webdevartisan\LaravelBlocker\Facades\BlockedIpStore;
use Webdevartisan\LaravelBlocker\Facades\LaravelBlocker;
use Closure;

class BlockMaliciousUsers
{
    public function handle($request, Closure $next)
    {
        // protection_enabled
        $protectionEnabled = config('laravel-shield.protection_enabled');
        if (!$protectionEnabled) {
            return $next($request);
        }

        $requestIp = request()->ip();

        // Is this a blocked IP?
        if (BlockedIpStore::has($requestIp)) {
            return response('You have been blocked', 401);
        }

        // Does this URL contain a malicious string?
        // @see config/config.php
        if (LaravelBlocker::isMailicousRequest()) {
            // Store blocked IP
            BlockedIpStore::create($requestIp);

            return response('Not accepted', 406);
        }

        // Does the request come from a malicious User Agent?
        // @see config/config.php
        if (LaravelBlocker::isMaliciousUserAgent()) {
            // Store blocked IP
            BlockedIpStore::create($requestIp);

            return response('Not accepted', 406);
        }

        // TODO: Another check if is malicious sql.

        return $next($request);
    }
}
