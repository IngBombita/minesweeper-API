<?php
declare(strict_types=1);

namespace Infrastructure\Cache;

use RuntimeException;

final class InvalidArgumentException extends RuntimeException implements \Psr\Cache\InvalidArgumentException
{

}
