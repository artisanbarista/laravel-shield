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
        return $this->checkMaliciousTerms(config('laravel-shield.malicious_urls'), request()->fullUrl());
    }

    public function isMaliciousUserAgent (): bool {
        return $this->checkMaliciousTerms(config('laravel-shield.malicious_user_agents'), request()->header('user-agent'));
    }

    public function isMaliciousPattern(): bool
    {
        return $this->checkMaliciousPatterns(config('laravel-shield.malicious_patterns'));
    }

    private function checkMaliciousTerms(array $terms, string $uri): bool
    {
        foreach ($terms as $term) {
            if (stripos($uri, $term) !== false) {
                return true;
            }
        }

        return false;
    }

    private function checkMaliciousPatterns($patterns): bool
    {
        foreach ($patterns as $pattern) {
            if ($this->matchMaliciousPatterns($pattern, request()->input())) {
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
                if (!$result = $this->matchMaliciousPatterns($pattern, $value)) {
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
