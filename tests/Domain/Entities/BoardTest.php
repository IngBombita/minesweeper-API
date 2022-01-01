<?php

namespace Tests\Domain\Entities;

use Domain\Entities\Board;
use Domain\Entities\Cell;
use Domain\Exceptions\InvalidParameters;
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
        $cell                = Cell::create(false, [0, 0]);
        $cellNeighbourMinedA = Cell::create(true, [0, 1]);
        $cellNeighbourMinedB = Cell::create(true, [1, 1]);
        $cellNeighbourFree   = Cell::create(false, [1, 1]);
        $sut                 = Board::create(4, [
            [$cell, $cellNeighbourMinedA],
            [$cellNeighbourFree, $cellNeighbourMinedB],
        ]);

        $cell = TestUtils::callMethod($sut, 'computeCellValue', [$cell]);

        self::assertEquals(2, $cell->getValue());
    }

    public function testGetNeighbours(): void
    {
        $sut        = Board::create(4, []);
        $neighbours = TestUtils::callMethod($sut, 'getNeighbours', [0, 0]);
        self::assertEqualsCanonicalizing(
            [
                [0,1],
                [1,0],
                [1,1],
            ]
        , $neighbours);

        $neighbours = TestUtils::callMethod($sut, 'getNeighbours', [3, 2]);
        self::assertEqualsCanonicalizing(
              [
                  [2,1],
                  [3,1],
                  [2,2],
                  [2,3],
                  [3,3],
              ]
            , $neighbours);
    }
}
