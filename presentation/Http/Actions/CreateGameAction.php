<?php

namespace Presentation\Http\Actions;

use Application\Services\CacheService;
use Domain\Entities\Game;
use Domain\Exceptions\InvalidParameters;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Factory;
use Psr\Http\Message\ServerRequestInterface;

class CreateGameAction
{
    public function __construct(private Factory $validatorFactory, private CacheService $cacheService)
    {
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $body = $request->getParsedBody();

        $validator = $this->validatorFactory->make($body, $this->getValidationRules(), ['required' => 'The :attribute field is required.']);
        if ($validator->fails()) {
            return Response::json(
                [
                    'error' => $validator->errors()->all(),
                ],
                400
            );
        }

        // TODO: Move this to application layer
        try {
            $game = Game::create($body['size'], $body['mines']);
            $this->cacheService->put('game-' . $game->getId(), $game, null);

            return Response::json($game);

        } catch (InvalidParameters $e) {
            return Response::json(
                [
                    'error' => $e->getMessage(),
                ],
                400
            );
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return Response::json(
                [
                    'error' => 'Internal Server Error',
                ],
                500
            );
        }
    }

    private function getValidationRules(): array
    {
        return [
            'size' => 'required|integer|min:3',
            'mines' => 'required|integer|min:1',
        ];
    }
}