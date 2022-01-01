<?php

namespace Tests\Application\Services;

use Application\Services\CacheService;
use Application\Services\GameService;
use Domain\Entities\Game;
use Domain\Enums\CellActions;
use Domain\Enums\GameStatus;
use Mockery;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    public function testCreateGameSuccessful(): void
    {
        $mockCacheService = Mockery::mock(CacheService::class);
        $mockCacheService
            ->shouldReceive('put')
            ->with(Mockery::any(), Mockery::any(), Mockery::mustBe(null));

        $mockCacheService
            ->shouldReceive('get')
            ->with('games');

        $mockCacheService
            ->shouldReceive('put')
            ->with('games', Mockery::type('array'), Mockery::mustBe(null));


        $sut  = new GameService($mockCacheService);
        $game = $sut->createGame(3, 5);
        self::assertNotNull($game);
    }

    public function testUpdateCellSuccessful(): void
    {
        $mockCacheService = Mockery::mock(CacheService::class);
        $mockGame         = Mockery::mock(Game::class);
        $mockCacheService
            ->shouldReceive('get')
            ->with('game-gameID')
            ->andReturn($mockGame);

        $mockGame->shouldReceive('flagCell')
            ->with(0, 1);
        $mockGame->shouldReceive('isFinished')
            ->andReturn(false);
        $mockGame->shouldReceive('getId')
            ->andReturn('gameID');

        $mockCacheService
            ->shouldReceive('put')
            ->with(Mockery::any(), Mockery::any(), Mockery::mustBe(null));

        $sut  = new GameService($mockCacheService);
        $game = $sut->updateCell('gameID', CellActions::FLAG, 0, 1);
        self::assertNotNull($game);
    }
}
