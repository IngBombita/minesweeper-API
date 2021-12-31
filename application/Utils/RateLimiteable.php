<?php
declare(strict_types=1);

namespace Application\Utils;

use Illuminate\Contracts\Bus\Dispatcher;

trait RateLimiteable
{
    public function getPayload(array $response, $job, Dispatcher $jobDispatcher): ?array
    {
        if (isset($response['payload'])) {
            return $response['payload'];
        }

        if (! $response['rate_limit']) {
            throw new \Exception('External service has not payload and no rate limit');
        }

        $job->delay($response['rate_limit']['retry_in']);
        $jobDispatcher->dispatch($job);

        return null;
    }
}
