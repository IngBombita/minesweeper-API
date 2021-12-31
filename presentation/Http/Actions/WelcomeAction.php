<?php
declare(strict_types=1);

namespace Presentation\Http\Actions;

use Psr\Http\Message\ServerRequestInterface;

class WelcomeAction extends BaseAction
{
    public function __invoke(ServerRequestInterface $request)
    {
        return $this->respondWithArray(
            [
                'version'      => config('app.version'),
                'description'  => 'Minesweeper API, Deviget Code Challenge',
                'current_date' => (new \DateTimeImmutable())->format(\DateTimeInterface::RFC3339),
            ]
        );
    }
}
