<?php

namespace Tests\Domain\Entities;

use Domain\Entities\Board;
use Domain\Entities\Cell;
use Domain\Entities\Game;
use Tests\TestCase;
use Tests\TestUtils;

class GameTest extends TestCase
{
    /**
     * These test needs more cases and refactors
     */

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
