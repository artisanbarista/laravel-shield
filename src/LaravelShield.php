<?php

namespace Webdevartisan\LaravelShield;

class LaravelShield
{
    public function isMaliciousRequest(): bool
    {
        return match (true) {
            $this->isMaliciousUri(request()->fullUrl()),
            $this->isMaliciousUserAgent(request()->header('user-agent')),
            $this->isMaliciousPattern(request()->input()) => true,
            default => false,
        };
    }

    public function isMaliciousUri($url): bool
    {
        return $this->checkMaliciousTerms(config('shield.malicious_urls'), $url);
    }

    public function isMaliciousUserAgent($agent): bool {
        return $this->checkMaliciousTerms(config('shield.malicious_user_agents'), $agent);
    }

    public function isMaliciousPattern($input): bool
    {
        return $this->checkMaliciousPatterns(config('shield.malicious_patterns'), $input);
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
