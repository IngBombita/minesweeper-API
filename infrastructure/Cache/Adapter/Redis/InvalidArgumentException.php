<?php
declare(strict_types=1);

namespace Infrastructure\Cache\Adapter\Redis;

use RuntimeException;

final class InvalidArgumentException extends RuntimeException implements \Psr\Cache\InvalidArgumentException
{

}
