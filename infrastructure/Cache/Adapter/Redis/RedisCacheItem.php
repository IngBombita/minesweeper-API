<?php
declare(strict_types=1);

namespace Infrastructure\Cache\Adapter\Redis;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

final class RedisCacheItem implements CacheItemInterface
{
    private string $key;
    private $value;
    private bool $isHit = false;
    private ?DateTimeInterface $expiresAt = null;

    public function __construct(
        string $key,
        $value = null,
        bool $isHit = false
    ) {
        $this->key = $key;
        $this->value = $value;
        $this->isHit = $isHit;
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isHit(): bool
    {
        return $this->isHit;
    }

    /**
     * @inheritDoc
     */
    public function set($value): self
    {
        $new = clone $this;
        $new->value = $value;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt($expiration): self
    {
        $new = clone $this;

        switch ($expiration) {
            case null:
                $new->expiresAt = null;
                break;
            case $expiration instanceof DateTimeInterface:
                $new->expiresAt = $expiration;
                break;
            default:
                throw new InvalidArgumentException("Invalid expiration");
        }

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter($time): self
    {
        $new = clone $this;

        switch ($time) {
            case null:
                $new->expiresAt = null;
                break;
            case is_int($time):
                $new->expiresAt = new DateTime(sprintf('+%d seconds', $time));
                break;
            default:
                throw new InvalidArgumentException("Invalid time");
        }

        return $new;
    }

    public function getExpiration(): DateTimeInterface
    {
        return $this->expiresAt ? $this->expiresAt : new DateTime();
    }
}
