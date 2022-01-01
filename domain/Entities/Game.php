<?php

namespace Domain\Entities;

use Domain\Enums\GameStatus;
use Domain\Exceptions\InvalidParameters;
use Domain\Exceptions\InvalidStateMutation;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Game extends Model
{

    public static function create(int $size, int $mines): Game
    {
        if ($mines > $size * $size) {
            throw new InvalidParameters("cannot be more mines than cells");
        }

        $game         = new self();
        $game->mines  = $mines;
        $game->board  = self::buildBoard($size, $mines);
        $game->status = GameStatus::CREATED;
        $game->uuid   = Uuid::uuid4();

        return $game;
    }

    public function serialize(): array
    {
        return [
            'mines'      => $this->mines,
            'board'      => $this->getBoard()->serialize(),
            'status'     => $this->status,
            'uuid'       => $this->uuid,
            'started_at' => $this->startedAt,
            'ended_at'   => $this->endedAt,
        ];
    }

    public static function unserialize(array $props): self
    {
        $game            = new self();
        $game->mines     = $props['mines'];
        $game->board     = Board::unserialize($props['board']);
        $game->status    = $props['status'];
        $game->uuid      = $props['uuid'];
        $game->startedAt = $props['started_at'];
        $game->endedAt   = $props['ended_at'];

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
            $this->finishGame(GameStatus::LOST);
            return;
        }

        $this->board->clickCell($cell);
        if ($this->checkWin()) {
            $this->finishGame(GameStatus::WON);
        }
    }

    private function checkWin(): bool
    {
        $cellsClicked = $this->getBoard()->getClickedCellQuantity();
        return $cellsClicked === $this->mines;
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
        $this->board->switchFlagCell($cell, true);
    }

    public function unFlagCell(int $row, int $column)
    {
        $cell = $this->board->getCell($row, $column);
        if (! $cell->isFlagged()) {
            throw new InvalidStateMutation("cell is not flagged");
        }
        $this->flagsAvailable++;
        $this->board->switchFlagCell($cell, false);
    }

    public function finishGame(string $status) : void
    {
        $finishedStatus = [GameStatus::LOST, GameStatus::WON];
        if (! in_array($status, $finishedStatus, true)) {
            throw new InvalidStateMutation('cannot end the game with status: ' . $status);
        }

        if ($status === GameStatus::LOST) {
            $this->status = GameStatus::LOST;
        } else {
            $this->status = GameStatus::WON;
        }
        $this->endedAt = new \DateTimeImmutable('now');
        $this->getBoard()->revealMines();
    }

    public function getBoard(): Board
    {
        return $this->board;
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function isFinished(): bool
    {
        $finishedStatus = [GameStatus::LOST, GameStatus::WON];
        return in_array($this->status, $finishedStatus, true);
    }
}
