<?php
declare(strict_types=1);

namespace Application\Utils;

class RemoveNullInArray
{
    public static function run(array $params): array
    {
        return array_filter(
            $params,
            static function ($param) {
                return ! is_null($param);
            }
        );
    }
}
