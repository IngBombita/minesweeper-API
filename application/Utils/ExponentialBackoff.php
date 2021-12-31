<?php

namespace Application\Utils;

trait ExponentialBackoff
{
    public function backoff(?int $retries = 10)
    {
        $backoffs = [];
        for ($i = 1; $i <= $retries; $i++) {
            $backoffs[] = pow(2, $i);
        }
        return $backoffs;
    }
}
