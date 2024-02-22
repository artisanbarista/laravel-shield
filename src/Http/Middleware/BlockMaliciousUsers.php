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

    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    protected bool $checkForMaliciousUrls;

    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    protected bool $checkForMaliciousUseragents;

    public function __construct()
    {
        $this->checkForMaliciousUrls = (bool) config('laravel-shield.url_detection_enabled');
        $this->checkForMaliciousUseragents = (bool) config('laravel-shield.user_agent_detection_enabled');
    }

    public function handle($request, Closure $next)
    {
        $requestIp = request()->ip();

        // Is this a blocked IP?
        if ($this->checkUrlsOrAgents() && BlockedIpStore::has($requestIp)) {
            return response('You have been blocked', 401);
        }

        // Does this URL contain a malicious string?
        // @see config/config.php
        if ($this->checkForMaliciousUrls && LaravelBlocker::isMailicousRequest()) {
            // Store blocked IP
            BlockedIpStore::create($requestIp);

            return response('Not accepted', 406);
        }

        // Does the request come from a malicious User Agent?
        // @see config/config.php
        if ($this->checkForMaliciousUseragents && LaravelBlocker::isMaliciousUserAgent()) {
            // Store blocked IP
            BlockedIpStore::create($requestIp);

            return response('Not accepted', 406);
        }

        // TODO: Another check if is malicious sql.

        return $next($request);
    }

    /**
     * @param mixed $checkForMaliciousUrls
     * @param mixed $checkForMaliciousUseragents
     *
     * @return bool
     */
    protected function checkUrlsOrAgents(): bool
    {
        return $this->checkForMaliciousUrls || $this->checkForMaliciousUseragents;
    }
}
