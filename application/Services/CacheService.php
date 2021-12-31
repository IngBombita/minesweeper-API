<?php

namespace Application\Services;

interface CacheService
{
    public function get(string $key);

    public function forget(string $key): void;

    public function forgetKeys(array $keys): void;

    public function put(string $key, $resource, ?int $ttlInSeconds = 604800): void;
}
