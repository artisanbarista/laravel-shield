<?php

namespace Artisanbarista\LaravelShield\Services;

use Illuminate\Support\Facades\RateLimiter;

class BlockedIpStoreRateLimiter implements BlockedIpStoreInterface
{

    public function create(string $ip, int $decaySeconds = null ): BlockedIpStoreInterface
    {
        RateLimiter::hit($this->getRateLimiterKey($ip), $this->getExpirationTime($decaySeconds));

        return $this;
    }

    public function delete(string $ip): BlockedIpStoreInterface
    {
        RateLimiter::clear($this->getRateLimiterKey($ip));

        return $this;
    }

    public function has(string $ip): bool
    {
        if (RateLimiter::remaining($this->getRateLimiterKey($ip), $this->getMaxAttempts())) {
            return false;
        }

        return true;
    }

    public function attempts(string $ip): int
    {
        return RateLimiter::attempts($this->getRateLimiterKey($ip));
    }

    protected function getMaxAttempts(): int
    {
        return config('shield.max_attempts');
    }

    protected function getRateLimiterKey($ip): string
    {
        return 'blocked:' . $ip;
    }

    protected function getExpirationTime(?int $decaySeconds): int
    {
        return $decaySeconds ?: config('shield.expiration_time');
    }
}
