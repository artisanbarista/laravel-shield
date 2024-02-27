<?php

namespace Webdevartisan\LaravelBlocker;

class LaravelBlocker
{

    public function isMaliciousRequest(): bool
    {
        return match (true) {
            $this->isMaliciousUri(),
            $this->isMaliciousUserAgent() => true,
            $this->isMaliciousPattern() => true,
            default => false,
        };
    }

    public function isMaliciousUri(): bool
    {
        return $this->checkMaliciousTerm(request()->fullUrl(), config('laravel-shield.malicious_urls'));
    }

    public function isMaliciousUserAgent () {
        return $this->checkMaliciousTerm($this->getUserAgent(), config('laravel-shield.malicious_user_agents'));
    }

    public function isMaliciousPattern(): bool
    {
        return !empty($this->check(config('laravel-shield.malicious_patterns')));
    }

    private function checkMaliciousTerm(array $conf, string $uri): bool
    {
        foreach ($conf as $malice) {
            if (stripos($uri, $malice) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getUserAgent () {
        return request()->header('user-agent');
    }

    private function check($patterns)
    {
        foreach ($patterns as $pattern) {
            if ($this->match($pattern, request()->input())) {
                return true;
            }
        }

        return false;
    }

    private function match($pattern, $input)
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
                if (!$result = $this->match($pattern, $value)) {
                    continue;
                }
                break;
            }

            if ($result = preg_match($pattern, $value)) {
                break;
            }

            break;
        }

        return $result;
    }
}
