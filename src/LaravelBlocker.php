<?php

namespace Webdevartisan\LaravelBlocker;

class LaravelBlocker
{

    public function isMaliciousRequest(): bool
    {
        return $this->isMaliciousUri(request()->fullUrl());
    }

    public function isMaliciousUri(string $uri): bool
    {
        $search = preg_quote(implode('|', config('laravel-shield.malicious_urls')), '/');
        $search = str_replace('\|', '|', $search);
        preg_match('/(' . $search . ')/i', $uri, $matches);

        if (empty($matches)) {
            return false;
        }

        return true;
    }

    public function getUserAgent () {
        return request()->header('user-agent');
    }

    public function isMaliciousUserAgent () {
        $search = preg_quote(implode('|', config('laravel-shield.malicious_user_agents')), '/');
        $search = str_replace('\|', '|', $search);
        preg_match('/(' . $search . ')/i', $this->getUserAgent(), $matches);

        if (empty($matches)) {
            return false;
        }

        return true;
    }
}
