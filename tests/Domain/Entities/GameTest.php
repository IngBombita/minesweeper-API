<?php

namespace Tests\Domain\Entities;

use Domain\Entities\Board;
use Domain\Entities\Cell;
use Domain\Entities\Game;
use Domain\Enums\GameStatus;
use Domain\Exceptions\InvalidParameters;
use Domain\Exceptions\InvalidStateMutation;
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
        $sut->board->shouldReceive('revealMines');

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
        $sut->board->shouldReceive('revealMines');
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
        $sut->board->shouldReceive('revealMines');

        $sut->clickCell(0,0);
        self::assertEquals(GameStatus::WON, $sut->status);
    }

    public function testFlagCellSuccessful(): void
    {
        $sut = Game::create(4, 5);
        $sut->flagsAvailable = 1;
        $cell = Cell::create(false,[1,0]);

        $sut->board = \Mockery::mock(Board::class);
        $sut->board->shouldReceive('getCell')
            ->with(1,0)
            ->andReturn($cell);
        $sut->board->shouldReceive('flagCell')
            ->with($cell, true);

        $sut->flagCell(1,0);
        self::assertEquals(0, $sut->flagsAvailable);
    }

    public function testFlagCellAlreadyFlaggedError(): void
    {
        $sut = Game::create(4, 5);
        $sut->flagsAvailable = 1;
        $cell = Cell::create(false,[1,0]);
        $cell->setFlagged(true);

        $sut->board = \Mockery::mock(Board::class);
        $sut->board->shouldReceive('getCell')
            ->with(1,0)
            ->andReturn($cell);

        $this->expectException(InvalidStateMutation::class);
        $this->expectExceptionMessage('cell was already flagged');

        $sut->flagCell(1,0);
        self::assertEquals(0, $sut->flagsAvailable);
    }

    public function testFlagCellNoMoreFlagAvailable(): void
    {
        $sut = Game::create(4, 5);
        $sut->flagsAvailable = 0;
        $cell = Cell::create(false,[1,0]);

        $sut->board = \Mockery::mock(Board::class);
        $sut->board->shouldReceive('getCell')
            ->with(1,0)
            ->andReturn($cell);

        $this->expectException(InvalidStateMutation::class);
        $this->expectExceptionMessage('cannot flag more cells than mines are in game');

        $sut->flagCell(1,0);
        self::assertEquals(0, $sut->flagsAvailable);
    }

    public function testUnFlagCellSuccessful(): void
    {
        $sut = Game::create(4, 5);
        $sut->flagsAvailable = 0;
        $cell = Cell::create(false,[1,0]);
        $cell->setFlagged(true);

        $sut->board = \Mockery::mock(Board::class);
        $sut->board->shouldReceive('getCell')
            ->with(1,0)
            ->andReturn($cell);

        $sut->board->shouldReceive('switchFlagCell')
            ->with($cell, false);

        $sut->unFlagCell(1,0);
        self::assertEquals(1, $sut->flagsAvailable);
    }

    public function testUnFlagCellNotFlagged(): void
    {
        $sut = Game::create(4, 5);
        $sut->flagsAvailable = 0;
        $cell = Cell::create(false,[1,0]);

        $sut->board = \Mockery::mock(Board::class);
        $sut->board->shouldReceive('getCell')
            ->with(1,0)
            ->andReturn($cell);

        $this->expectException(InvalidStateMutation::class);
        $this->expectExceptionMessage('cell is not flagged');

        $sut->unFlagCell(1,0);
        self::assertEquals(1, $sut->flagsAvailable);
    }

    public function testFinishGameSucessful(): void
    {
        $sut = Game::create(4, 5);

        $sut->board = \Mockery::mock(Board::class);
        $sut->board->shouldReceive('revealMines');

        $sut->finishGame(GameStatus::WON);
        self::assertEquals(GameStatus::WON, $sut->status);
    }

    public function testFinishGameFail(): void
    {
        $sut = Game::create(4, 5);

        $this->expectException(InvalidStateMutation::class);
        $this->expectExceptionMessage('cannot end the game with status: playing');

        $sut->finishGame(GameStatus::PLAYING);
        self::assertEquals(GameStatus::CREATED, $sut->status);
    }

    public function testBoardCellValue(): void
    {
        $sut   = $this->getExampleGame();
        $board = $sut->getBoard();

        self::assertEquals(2, $this->callComputeCellValue($board, 0,0));
        self::assertEquals(null, $this->callComputeCellValue($board,0, 1));
        self::assertEquals(3, $this->callComputeCellValue($board,1, 1));
        self::assertEquals(2, $this->callComputeCellValue($board,2, 0));
        self::assertEquals(2, $this->callComputeCellValue($board,2, 1));
        self::assertEquals(1, $this->callComputeCellValue($board,2, 2));
        self::assertEquals(1, $this->callComputeCellValue($board,2, 3));
        self::assertEquals(null, $this->callComputeCellValue($board,3, 0));
        self::assertEquals(1, $this->callComputeCellValue($board,3, 1));
        self::assertEquals(0, $this->callComputeCellValue($board,3, 2));
        self::assertEquals(0, $this->callComputeCellValue($board,3, 3));
    }

    private function callComputeCellValue($board, int $row, int $column): ?int {
        return TestUtils::callMethod($board,'computeCellValue',[$board->getCell($row, $column)])->getValue();
    }

    public function testClickCellSuccess(): void
    {
        $sut   = $this->getExampleGame();
        $board = $sut->getBoard();
        $sut->clickCell(3, 3);

        self::assertEquals(null, $board->getCell(3, 0)->getValue());
        self::assertFalse($board->getCell(3, 0)->isClicked());
        self::assertTrue($board->getCell(3, 1)->isClicked());
        self::assertTrue($board->getCell(3, 2)->isClicked());
        self::assertTrue($board->getCell(3, 3)->isClicked());
        self::assertTrue($board->getCell(2, 3)->isClicked());
        self::assertTrue($board->getCell(2, 2)->isClicked());
    }

    private function getExampleGame(): Game
    {
        /**
         * 0  1  1  0
         * 1  0  0  1
         * 0  0  0  0
         * 1  0  0  0
         */
        $cells       = [
            [
                Cell::create(false, [0, 0]),
                Cell::create(true, [0, 1]),
                Cell::create(true, [0, 2]),
                Cell::create(false, [0, 3]),
            ],
            [
                Cell::create(true, [1, 0]),
                Cell::create(false, [1, 1]),
                Cell::create(false, [1, 2]),
                Cell::create(true, [1, 3]),
            ],
            [
                Cell::create(false, [2, 0]),
                Cell::create(false, [2, 1]),
                Cell::create(false, [2, 2]),
                Cell::create(false, [2, 3]),
            ],
            [
                Cell::create(true, [3, 0]),
                Cell::create(false, [3, 1]),
                Cell::create(false, [3, 2]),
                Cell::create(false, [3, 3]),
            ],
        ];
        $game        = Game::create(4, 5);
        $game->board = Board::create(4, $cells);
        return $game;
    }
}
