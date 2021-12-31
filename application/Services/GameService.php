<?php

namespace Application\Services;

use Application\Exceptions\NotFound;
use Domain\Entities\Game;
use Domain\Enums\CellActions;
use Domain\Exceptions\InvalidParameters;

class GameService
{
    private const GAMES_ID_KEY = 'games';

    public function __construct(private CacheService $cacheService)
    {
    }

    public function createGame(int $size, int $mines): Game
    {
        $game = Game::create($size, $mines);

        $gameKey = 'game-' . $game->getId();
        $this->cacheService->put($gameKey, $game, null);

        $games = $this->cacheService->get(self::GAMES_ID_KEY);
        $games[] = $gameKey;
        $this->cacheService->put('games', $games, null);

        return $game;
    }

    public function updateCell(string $gameId, string $action, int $row, int $column): Game
    {
        $game = $this->retrieveGame($gameId);

        switch ($action) {
            case CellActions::CLICK:
                $game->clickCell($row, $column);
                break;
            case CellActions::FLAG:
                $game->flagCell($row, $column);
                break;
            case CellActions::UNFLAG:
                $game->unFlagCell($row, $column);
                break;
            default:
                throw new InvalidParameters('Action ' . $action . ' cannot be performed in a cell');
        }

        return $game;
    }

    public function retrieveGame(string $gameId): Game
    {
        $game = $this->cacheService->get('game-' . $gameId);
        if (! $game) {
            throw new NotFound("Game not found with id: " . $gameId);
        }
        return $game;
    }

    public function listGames(): array
    {
        $games = $this->cacheService->get(self::GAMES_ID_KEY);
        return $this->cacheService->getMany($games);
    }

    public function storeGame(Game $game): void
    {
        $this->cacheService->put('game-' . $game->getId(), $game, null);
    }
}
