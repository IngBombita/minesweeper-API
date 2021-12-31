<?php

namespace Tests\Domain\Entities;

use Domain\Entities\Board;
use Domain\Entities\Cell;
use Domain\Entities\Game;
use Tests\TestCase;

class GameTest extends TestCase
{
    /**
     * These test needs more cases and refactors
     */

    public function testBoardCellValue(): void
    {
        $sut   = $this->getExampleGame();
        $board = $sut->getBoard();
        $board->fillCellsValues();

        self::assertEquals(2, $board->getCell(0, 0)->getValue());
        self::assertEquals(null, $board->getCell(0, 1)->getValue());
        self::assertEquals(3, $board->getCell(1, 1)->getValue());
        self::assertEquals(2, $board->getCell(2, 0)->getValue());
        self::assertEquals(2, $board->getCell(2, 1)->getValue());
        self::assertEquals(1, $board->getCell(2, 2)->getValue());
        self::assertEquals(1, $board->getCell(2, 3)->getValue());
        self::assertEquals(null, $board->getCell(3, 0)->getValue());
        self::assertEquals(1, $board->getCell(3, 1)->getValue());
        self::assertEquals(0, $board->getCell(3, 2)->getValue());
        self::assertEquals(0, $board->getCell(3, 3)->getValue());
    }

    public function testClickCellSuccess(): void
    {
        $sut   = $this->getExampleGame();
        $board = $sut->getBoard();
        $board->fillCellsValues();
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
        $board           = [
            [new Cell(false, [0, 0]), new Cell(true, [0, 1]), new Cell(true, [0, 2]), new Cell(false, [0, 3]),],
            [new Cell(true, [1, 0]), new Cell(false, [1, 1]), new Cell(false, [1, 2]), new Cell(true, [1, 3]),],
            [new Cell(false, [2, 0]), new Cell(false, [2, 1]), new Cell(false, [2, 2]), new Cell(false, [2, 3]),],
            [new Cell(true, [3, 0]), new Cell(false, [3, 1]), new Cell(false, [3, 2]), new Cell(false, [3, 3]),],
        ];
        $game            = Game::create(4, 5);
        $gameReflection  = new \ReflectionClass($game);
        $boardReflection = $gameReflection->getProperty('board');
        $boardReflection->setAccessible(true);
        $boardReflection->setValue($game, new Board(4, $board));

        return $game;
    }
}
