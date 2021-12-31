<?php

namespace Presentation\Http\Actions;

use Application\Exceptions\NotFound;
use Application\Services\CacheService;
use Application\Services\GameService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Factory;
use Psr\Http\Message\ServerRequestInterface;

class GetGameStatsAction
{
    public function __construct(private Factory $validatorFactory, private GameService $gameService)
    {
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        try {
            $game = $this->gameService->retrieveGame($id);
            return Response::json($game);

        } catch (NotFound $e) {
            return Response::json(['error' => 'Game not found',], 404);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return Response::json(['error' => 'Internal Server Error',], 500);
        }
    }
}
