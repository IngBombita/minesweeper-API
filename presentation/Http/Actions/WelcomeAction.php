<?php
declare(strict_types=1);

namespace Presentation\Http\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Psr\Http\Message\ServerRequestInterface;

class WelcomeAction
{
    public function __invoke(ServerRequestInterface $request): JsonResponse
    {
        return Response::json(
            [
                'version'      => config('app.version'),
                'description'  => 'Minesweeper API, Deviget Code Challenge',
                'current_date' => (new \DateTimeImmutable())->format(\DateTimeInterface::RFC3339),
            ]
        );
    }
}
