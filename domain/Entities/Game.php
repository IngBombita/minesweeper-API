<?php

namespace Domain\Entities;

use Domain\Enums\GameStatus;
use Domain\Exceptions\InvalidParameters;
use Domain\Exceptions\InvalidStateMutation;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Game extends Model
{
    private const MINIMUM_SIZE = 2;

    public static function create(int $size, int $mines): Game
    {
        if ($size < self::MINIMUM_SIZE) {
            throw new InvalidParameters("size cannot be minor than " . self::MINIMUM_SIZE);
        }
        if ($mines > $size * $size) {
            throw new InvalidParameters("cannot be more mines than celles ");
        }

        $game         = new self();
        $game->size   = $size;
        $game->mines  = $mines;
        $game->board  = self::buildBoard($size, $mines);
        $game->status = GameStatus::CREATED;
        $game->uuid   = Uuid::uuid4();

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

            $cells[$row][$column] = Cell::create($isMined, [$row, $column]);
        }

        return Board::create($size, $cells);
    }

    public function clickCell(int $row, int $column)
    {
        if ($this->status != GameStatus::PLAYING) {
            $this->status    = GameStatus::PLAYING;
            $this->startedAt = new \DateTimeImmutable('now');
        }

        $cell = $this->board->getCell($row, $column);
        if ($cell->isClicked()) {
            throw new InvalidStateMutation("cell was already clicked");
        }

        if ($cell->isMined()) {
            $this->loose();
        }

        $this->board->clickCell($cell);
    }

    public function flagCell(int $row, int $column)
    {
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

    public function unFlagCell(int $row, int $column)
    {
        $cell = $this->board->getCell($row, $column);
        if (! $cell->isFlagged()) {
            throw new InvalidStateMutation("cell is not flagged");
        }
        $this->flagsAvailable++;
        $this->board->flagCell($cell, false);
    }

    public function loose()
    {
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

    public function getId(): string
    {
        return $this->uuid;
    }
}
