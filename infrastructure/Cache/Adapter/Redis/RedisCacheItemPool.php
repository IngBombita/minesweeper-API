<?php
declare(strict_types=1);

namespace Infrastructure\Cache\Adapter\Redis;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Redis;
use RuntimeException;

final class RedisCacheItemPool implements CacheItemPoolInterface
{
    private Redis $redis;
    private array $deferred = [];

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @inheritDoc
     */
    public function getItem($key): CacheItemInterface
    {
        if (! is_string($key)) {
            throw new InvalidArgumentException('Cache key must be a legal string');
        }

        if ($this->redis->exists($key) === 1) {
            $value = $this->redis->get($key);
            $ttl  = $this->redis->ttl($key);

            $time = $ttl !== false ? $ttl : null;

            $item = new RedisCacheItem($key, $value, true);

            return $item->expiresAfter($time);
        } else {
            return new RedisCacheItem($key);
        }
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = []): array
    {
        foreach ($keys as $key) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('Cache key must be a legal string');
            }
        }

        $values = $this->redis->mget($keys);

        $results = array_combine($keys, $values);

        $cacheItems = [];
        foreach ($results as $key => $value) {
            if ($value !== false) {
                $ttl = $this->redis->ttl($key);

                $time = $ttl !== false ? $ttl : null;

                $item = new RedisCacheItem($key, $value, true);

                $cacheItems[] = $item->expiresAfter($time);
            } else {
                $cacheItems[] = new RedisCacheItem($key);
            }
        }

        return $cacheItems;
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key): bool
    {
        if (! is_string($key)) {
            throw new InvalidArgumentException('Cache key must be a legal string');
        }

        return $this->redis->exists($key) === 1;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return $this->redis->flushAll();
    }

    /**
     * @inheritDoc
     */
    public function deleteItem($key): bool
    {
        if (! is_string($key)) {
            throw new InvalidArgumentException('Cache key must be a legal string');
        }

        $result = $this->redis->del($key);

        return $result === 1;
    }

    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('Cache key must be a legal string');
            }
        }

        $this->redis->del($keys);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        if (! $item instanceof RedisCacheItem) {
            throw new RuntimeException('CacheItem should be an instance of RedisCacheItem');
        }

        $timeout = $item->getExpiration()->getTimestamp() - time();
        $couldHaveSaved = $this->redis->set($item->getKey(), $item->get(), $timeout);

        return $couldHaveSaved === true;
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        if (! $item instanceof RedisCacheItem) {
            throw new RuntimeException('CacheItem should be an instance of RedisCacheItem');
        }

        $this->deferred[] = $item;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        $transaction = $this->redis->multi(Redis::MULTI);

        foreach ($this->deferred as $key => $item) {
            $timeout = $item->getExpiration()->getTimestamp() - time();

            $transaction->set($item->getKey(), $item->get(), $timeout);

            unset($this->deferred[$key]);
        }

        $transaction->exec();

        return true;
    }
}
