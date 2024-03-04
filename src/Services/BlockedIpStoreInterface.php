<?php

namespace Webdevartisan\LaravelShield\Services;

interface BlockedIpStoreInterface
{

    public function create(string $ip, int $expirationTimeInSeconds = null): BlockedIpStoreInterface;

    public function delete(string $ip): BlockedIpStoreInterface;

    public function has(string $ip): bool;

    public function attempts(string $ip): int;
}
