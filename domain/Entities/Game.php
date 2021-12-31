<?php

namespace Domain\Entities;

use Domain\Enums\cellStatus;
use Domain\Enums\GameStatus;
use Domain\Exceptions\InvalidParameters;
use Domain\Exceptions\InvalidStateMutation;

class Game
{
    private const MINIMUM_SIZE = 2;

    private string $status;
    private \DateTimeImmutable $startedAt;
    private int $flagsAvailable = 10;

    private function __construct(
        private int   $size,
        private int   $mines,
        private Board $board,
    ) {
        $this->status    = GameStatus::CREATED;
        $this->startedAt = new \DateTimeImmutable('now');
    }

    public static function create(int $size, int $mines): Game
    {
        if ($size < self::MINIMUM_SIZE) {
            throw new InvalidParameters("size cannot be minor than " . self::MINIMUM_SIZE);
        }
        if ($mines > $size * $size) {
            throw new InvalidParameters("cannot be more mines than celles ");
        }

        $game = new self($size, $mines, self::buildBoard($size, $mines));
        $game->getBoard()->fillCellsValues();
        return $game;
    }

    private static function buildBoard(int $size, int $mines): Board
    {
        $numbers = range(0, $size * $size);
        shuffle($numbers);
        $minesCoordinates = array_slice($numbers, 0, $mines);

        $cells = [];
        for ($index = 0; $index < $size * $size; $index++) {
            $row     = (int) floor($index / $size);
            $column  = $index % $size;
            $isMined = in_array($index, $minesCoordinates, true);

            $cells[$row][$column] = new Cell($isMined, [$row, $column]);
        }

        return new Board($size, $cells);
    }

    public function clickCell(int $row, int $column)
    {
        $cell = $this->board->getCell($row, $column);
        if ($cell->isClicked()) {
            throw new InvalidStateMutation("cell was already clicked");
        }

        if ($cell->isMined()) {
            $this->loose();
        }

        $this->board->clickCell($cell);
    }

    public function flagCell(int $row, int $column) {
        if ($this->flagsAvailable === 0) {
            throw new InvalidStateMutation("cannot flag more cells than mines are in game");
        }

        $cell = $this->board->getCell($row, $column);
        if ($cell->isFlagged()) {
            throw new InvalidStateMutation("cell was already flagged");
        }

        $this->flagsAvailable--;
        $this->board->flagCell($cell, true);
    }

    public function unFlagCell(int $row, int $column) {
        $cell = $this->board->getCell($row, $column);
        if (!$cell->isFlagged()) {
            throw new InvalidStateMutation("cell is not flagged");
        }
        $this->flagsAvailable++;
        $this->board->flagCell($cell, false);
    }

    public function loose() {
        $this->status = GameStatus::LOST;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMines(): int
    {
        return $this->mines;
    }

    public function getBoard(): Board
    {
        return $this->board;
    }
}
