<?php

namespace Tests\Domain\Entities;

use Domain\Entities\Board;
use Domain\Entities\Cell;
use Domain\Entities\Game;
use Domain\Enums\GameStatus;
use Domain\Exceptions\InvalidParameters;
use Tests\TestCase;
use Tests\TestUtils;

class GameTest extends TestCase
{
    public function testCreateGameSuccessful(): void
    {
        $sut = Game::create(4, 5);
        self::assertNotNull($sut);

        $sut = Game::create(4, 16);
        self::assertNotNull($sut);
    }

    public function testCreateGameInvalidParameters(): void
    {
        $this->expectException(InvalidParameters::class);
        $this->expectExceptionMessage('size cannot be minor than 2');
        Game::create(0, 5);

        $this->expectException(InvalidParameters::class);
        $this->expectExceptionMessage('cannot be more mines than cells');
        Game::create(4, 17);
    }

    public function testClickCellSuccessful(): void
    {
        $sut = Game::create(4, 5);
        $cell = Cell::create(false,[0,0]);

        $sut->board = \Mockery::mock(Board::class);
        $sut->board->shouldReceive('getCell')
            ->with(0,0)
            ->andReturn($cell);
        $sut->board->shouldReceive('clickCell')
            ->with($cell);
        $sut->board->shouldReceive('getClickedCellQuantity')
            ->andReturn(0);

        $sut->clickCell(0,0);
        self::assertEquals(GameStatus::PLAYING, $sut->status);
    }

    public function testClickCellWinSuccessful(): void
    {
        $sut  = Game::create(4, 5);
        $cell = Cell::create(false, [0, 0]);

        $sut->board = \Mockery::mock(Board::class);
        $sut->board->shouldReceive('getCell')
            ->with(0, 0)
            ->andReturn($cell);
        $sut->board->shouldReceive('clickCell')
            ->with($cell);
        $sut->board->shouldReceive('getClickedCellQuantity')
            ->andReturn(5);

        $sut->clickCell(0, 0);
        self::assertEquals(GameStatus::WON, $sut->status);
    }
    public function testClickCellLooseSuccessful(): void
    {
        $sut = Game::create(4, 5);
        $cell = Cell::create(true,[0,0]);

        $sut->board = \Mockery::mock(Board::class);
        $sut->board->shouldReceive('getCell')
            ->with(0,0)
            ->andReturn($cell);
        $sut->clickCell(0,0);

        self::assertEquals(GameStatus::LOST, $sut->status);
    }

    public function testClickCellInvalid(): void
    {
        $sut = Game::create(4, 5);
        $cell = Cell::create(false,[0,0]);

        $sut->board = \Mockery::mock(Board::class);
        $sut->board->shouldReceive('getCell')
            ->with(0,0)
            ->andReturn($cell);
        $sut->board->shouldReceive('clickCell')
            ->with($cell);
        $sut->board->shouldReceive('getClickedCellQuantity')
            ->andReturn(5);

        $sut->clickCell(0,0);
        self::assertEquals(GameStatus::WON, $sut->status);
    }
}
