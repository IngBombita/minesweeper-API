<?php

namespace Tests\Domain\Entities;

use Domain\Entities\Board;
use Domain\Entities\Cell;
use Domain\Exceptions\InvalidParameters;
use Domain\Exceptions\InvalidStateMutation;
use Tests\TestCase;
use Tests\TestUtils;

class BoardTest extends TestCase
{
    public function testCreateBoardSuccessful(): void
    {
        $sut = Board::create(4, []);
        self::assertNotNull($sut);
    }

    public function testCreateBoardFail(): void
    {
        $this->expectException(InvalidParameters::class);
        $this->expectExceptionMessage('size cannot be minor than 3');

        Board::create(2, []);
    }

    public function testComputeCellValueMined(): void
    {
        $cell = Cell::create(true, [0, 0]);
        $sut  = Board::create(4, [[$cell]]);

        $result = TestUtils::callMethod($sut, 'computeCellValue', [$cell]);
        self::assertEquals($cell, $result);
    }

    public function testComputeCellSuccessful(): void
    {
        $cell = Cell::create(false, [0, 0]);
        $sut  = $this->getExampleBoard($cell);

        $cell = TestUtils::callMethod($sut, 'computeCellValue', [$cell]);

        self::assertEquals(2, $cell->getValue());
    }

    public function testGetNeighbours(): void
    {
        $sut        = Board::create(4, []);
        $neighbours = TestUtils::callMethod($sut, 'getNeighbours', [0, 0]);
        self::assertEqualsCanonicalizing(
            [
                [0, 1],
                [1, 0],
                [1, 1],
            ],
            $neighbours
        );

        $neighbours = TestUtils::callMethod($sut, 'getNeighbours', [3, 2]);
        self::assertEqualsCanonicalizing(
            [
                [2, 1],
                [3, 1],
                [2, 2],
                [2, 3],
                [3, 3],
            ],
            $neighbours
        );
    }

    public function testClickCellSuccessful(): void
    {
        $cell = Cell::create(false, [0, 0]);
        $sut  = $this->getExampleBoard($cell);

        $sut->clickCell($cell);

        self::assertTrue($cell->isClicked());
        self::assertEquals(2, $cell->getValue());

        $cell = $sut->getCell(1,0);
        self::assertTrue($cell->isClicked());
        self::assertEquals(3, $cell->getValue());

        $cell = $sut->getCell(0,1);
        self::assertTrue($cell->isMined());
        self::assertFalse($cell->isClicked());
        self::assertEquals(null, $cell->getValue());

        $cell = $sut->getCell(0,2);
        self::assertFalse($cell->isClicked());
        self::assertEquals(null, $cell->getValue());
    }

    public function testClickCellAlreadyClicked(): void
    {
        $cell = Cell::create(false, [0, 0]);
        $cell->click();
        $sut = Board::create(4, [[$cell]]);

        $this->expectException(InvalidStateMutation::class);
        $this->expectExceptionMessage("the cell had already been clicked");

        $sut->clickCell($cell);
    }

    public function testSwitchFlagCellSuccessful(): void
    {
        $cell = Cell::create(true, [0, 0]);
        $sut  = Board::create(4, [[$cell]]);

        $sut->switchFlagCell($cell, true);

        self::assertTrue($cell->isFlagged());

        $updatedCell = $sut->getCell(0,0);
        self::assertEquals($cell, $updatedCell);
    }

    public function testGetClickedCellsQuantity(): void
    {
        $cell = Cell::create(false, [0, 0]);
        $sut  = $this->getExampleBoard($cell);

        $sut->getCell(0,1)->click();
        $sut->getCell(1,1)->click();
        $sut->getCell(2,1)->click();
        $sut->getCell(2,2)->click();

        self::assertEquals(4, $sut->getClickedCellsQuantity());
    }

    private function getExampleBoard(Cell $cell): Board
    {
        $cellNeighbourMinedA = Cell::create(true, [0, 1]);
        $cellNeighbourMinedB = Cell::create(true, [1, 1]);
        $cellNeighbourMinedC = Cell::create(true, [1, 2]);
        $cellNeighbourMinedD = Cell::create(true, [2, 1]);
        $cellNeighbourFreeA  = Cell::create(false, [1, 0]);
        $cellNeighbourFreeB  = Cell::create(false, [0, 2]);
        $cellNeighbourFreeC  = Cell::create(false, [2, 0]);
        $cellNeighbourFreeD  = Cell::create(false, [2, 2]);
        return Board::create(3, [
            [$cell, $cellNeighbourMinedA, $cellNeighbourFreeB],
            [$cellNeighbourFreeA, $cellNeighbourMinedB, $cellNeighbourMinedC],
            [$cellNeighbourFreeC, $cellNeighbourMinedD, $cellNeighbourFreeD],
        ]);
    }
}
