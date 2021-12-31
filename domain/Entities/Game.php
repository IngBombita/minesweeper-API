<?php

namespace Domain\Entities;

use Domain\Enums\BoxStatus;
use Domain\Enums\GameStatus;
use Domain\Exceptions\InvalidParameters;
use Domain\Exceptions\InvalidStateMutation;

class Game
{
    private const MINIMUM_SIZE = 2;

    private string $status;
    private \DateTimeImmutable $startedAt;

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
            throw new InvalidParameters("cannot be more mines than boxes ");
        }

        return new self($size, $mines, self::buildBoard($size, $mines));
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

    public function clickBox(int $x, int $y)
    {
        $box = $this->board[$x][$y];
        if ($box->isClicked()) {
            throw new InvalidStateMutation("box was already clicked");
        }

        if ($box->isMined()) {
            $this->status = GameStatus::LOST;

        }
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMines(): int
    {
        return $this->mines;
    }

    // This is temporary, i dont like the idea of have a board getter
    public function getBoard(): Board
    {
        return $this->board;
    }
}
