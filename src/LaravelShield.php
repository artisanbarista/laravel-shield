<?php

namespace Webdevartisan\LaravelShield;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LaravelShield
{
    private string $matchDescription;

    public function isMaliciousRequest(): bool
    {
        return match (true) {
            $this->isMaliciousUri(request()->fullUrl()),
            $this->isMaliciousUserAgent(request()->userAgent()),
            $this->isMaliciousCookie(request()->cookies->all()),
            $this->isMaliciousPatternPath(request()->path()),
            $this->isMaliciousPatternInput(request()->input()) => true,
            default => false,
        };
    }

    public function getMatchDescription(): string
    {
        return $this->matchDescription;
    }

    public function isMaliciousCookie($cookies): bool
    {
        if ($match = $this->checkMaliciousPatterns(config('shield.malicious_cookie_patterns'), $cookies)) {
            $this->matchDescription = "Malicious cookies.";
        }
        return $match;
    }

    public function isMaliciousUri($url): bool
    {
        if ($match = (
            $this->whitelistUtms() && $this->checkMaliciousTerms(config('shield.malicious_urls'), urldecode($url)))
        ) {
            $this->matchDescription = "Malicious URI.";
        }

        return $match;
    }

    public function isMaliciousUserAgent($agent): bool
    {
        $description = "Malicious user agent.";
        if(!is_string($agent) || empty($agent)) {
            $this->matchDescription = $description;
            return true;
        }

        if ($match = $this->checkMaliciousTerms(config('shield.malicious_user_agents'), $agent)) {
            $this->matchDescription = $description;
        }
        return $match;
    }

    public function isMaliciousPatternInput($input): bool
    {
        if ($match = $this->checkMaliciousPatterns(config('shield.malicious_patterns'), $input)) {
            $this->matchDescription = "Malicious pattern in input.";
        }
        return $match;
    }

    public function isMaliciousPatternPath($path): bool
    {
        if ($match = $this->checkMaliciousPatterns(config('shield.malicious_patterns'), $path)) {
            $this->matchDescription = "Malicious pattern in path.";
        }
        return $match;
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

    private function whitelistUtms(): bool
    {
        $utms = ['utm_medium', 'utm_source', 'utm_campaign', 'utm_content', 'utm_term'];

        if (!count(request()->only($utms)) === count(request()->query()) && request()->has($utms)) {
            return false;
        }

        return true;
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
