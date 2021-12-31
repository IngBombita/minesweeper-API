<?php

namespace Domain\Entities;

use Domain\Enums\BoxStatus;
use Domain\Enums\GameStatus;
use Domain\Exceptions\InvalidParameters;

class Game
{
    private const MINIMUM_SIZE = 2;

    private string $status;
    private \DateTimeImmutable $startedAt;

    private function __construct(
        private int   $size,
        private int   $mines,
        private array $board,
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

    private static function buildBoard(int $size, int $mines): array {
        $numbers = range(0, $size * $size);
        shuffle($numbers);
        $minesCoordinates = array_slice($numbers, 0, $mines);

        $board = [];
        for ($index = 0; $index < $size * $size; $index++) {
            $board[floor($index / $size)][$index % $size] = new Box(in_array($index, $minesCoordinates, true));
        }

        return $board;
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
    public function getBoard(): array
    {
        return $this->board;
    }
}
