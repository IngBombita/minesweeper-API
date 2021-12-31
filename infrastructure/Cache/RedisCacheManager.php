<?php

declare(strict_types=1);

namespace Infrastructure\Cache;

use Application\Services\CacheService;
use Illuminate\Cache\Repository;

class RedisCacheManager implements CacheService
{
    private const ONE_WEEK_TTL = 604800;
    private Repository $cacheRepository;

    public function __construct(Repository $cacheRepository)
    {
        $this->cacheRepository = $cacheRepository;
    }

    public function forget(string $key): void
    {
        $this->cacheRepository->forget($key);
    }

    public function put(string $key, $resource, ?int $ttlInSeconds = 604800): void
    {
        $this->cacheRepository->put($key, serialize($resource), $ttlInSeconds);
    }

    public function get(string $key)
    {
        $resource = $this->cacheRepository->get($key);
        if (is_null($resource)) {
            return null;
        }

        return unserialize($resource, [true]);
    }

    public function getMany($keys)
    {
        return array_map(function (string $key) {
            return $this->get($key);
        }, $keys);
    }

    public function forgetKeys(array $keys): void
    {
        foreach ($keys as $key) {
            $this->forget($key);
        }
    }
}
