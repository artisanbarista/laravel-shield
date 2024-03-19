<?php

namespace Webdevartisan\LaravelShield;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LaravelShield
{
    private string $matchDescription;

    public function isMaliciousRequest(): bool
    {
        if (count(request()->only(['utm_medium', 'utm_source', 'utm_campaign', 'utm_content', 'utm_term'])) === 5) {
            return false;
        }

        if ($this->isMaliciousUserAgent(request()->userAgent())) {
            $this->matchDescription = "Malicious user agent found.";
            return true;
        }
        if ($this->isMaliciousUri(request()->fullUrl())) {
            $this->matchDescription = "Malicious URI found.";
            return true;
        }
        if ($this->isMaliciousCookie(request()->cookies->all())) {
            $this->matchDescription = "Malicious cookies found.";
            return true;
        }
        if ($this->isMaliciousPattern(request()->path())) {
            $this->matchDescription = "Malicious pattern in path found.";
            return true;
        }
        if ($this->isMaliciousPattern(request()->input())) {
            $this->matchDescription = "Malicious pattern in input found.";
            return true;
        }

        return false;
    }

    public function getMatchDescription(): string
    {
        return $this->matchDescription;
    }

    public function isMaliciousCookie($cookies): bool
    {
        return $this->checkMaliciousPatterns(config('shield.malicious_cookie_patterns'), $cookies);
    }

    public function isMaliciousUri($url): bool
    {
        return $this->checkMaliciousTerms(config('shield.malicious_urls'), urldecode($url));
    }

    public function isMaliciousUserAgent($agent): bool
    {
        if(!is_string($agent) || empty($agent)) {
            return true;
        }

        return $this->checkMaliciousTerms(config('shield.malicious_user_agents'), $agent);
    }

    public function isMaliciousPattern($input): bool
    {
        return $this->checkMaliciousPatterns(config('shield.malicious_patterns'), $input);
    }

    public function isValidBot($ip) : bool
    {
        $host = gethostbyaddr($ip);
        $ipAfterLookup = gethostbyname($host);

        if ($host === $ipAfterLookup) {
            return false;
        }

        $hostIsValid = !!array_filter(config('shield.whitelist_hosts'), function ($validHost) use ($host) {
            return Str::endsWith($host, $validHost);
        });

        return $hostIsValid && $ipAfterLookup === $ip;
    }

    public function log($message): void
    {
        if (!config('shield.logging_enabled')) {
            return;
        }

        Log::notice($message);
    }

    private function checkMaliciousTerms(array $terms, string $malice): bool
    {
        foreach ($terms as $term) {
            if (stripos($malice, $term) !== false) {
                return true;
            }
        }

        return false;
    }

    private function checkMaliciousPatterns(array $patterns, mixed $malice): bool
    {
        foreach ($patterns as $pattern) {
            if ($this->matchMaliciousPatterns($pattern, $malice)) {
                return true;
            }
        }

        return false;
    }

    private function matchMaliciousPatterns($pattern, $input)
    {
        $result = false;

        if (! is_array($input) && !is_string($input)) {
            return false;
        }

        if (! is_array($input)) {
            return preg_match($pattern, $input);
        }

        foreach ($input as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if (is_array($value)) {
                if ($result = $this->matchMaliciousPatterns($pattern, $value)) {
                    break;
                }
                continue;
            }

            if ($result = preg_match($pattern, $value)) {
                break;
            }
        }

        return $result;
    }
}
