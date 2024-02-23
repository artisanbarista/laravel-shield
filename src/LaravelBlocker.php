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

    public function isMaliciousPattern(): bool
    {
        return !empty($this->check(config('laravel-shield.malicious_patterns')));
    }

    public function check($patterns)
    {
        foreach ($patterns as $pattern) {
            if ($this->match($pattern, request()->input())) {
                return true;
            }
        }
        return false;
    }

    public function match($pattern, $input)
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
