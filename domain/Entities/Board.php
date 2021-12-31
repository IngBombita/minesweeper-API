<?php

namespace Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    public static function create(int $size, array $cells): self
    {
        $board        = new self();
        $board->size  = $size;
        $board->cells = $cells;

        return $board;
    }

    private function computeCellValue(Cell $cell): Cell
    {
        if ($cell->isMined()) {
            return $cell;
        }

        $neighbours = $this->getNeighbours(...$cell->getPosition());
        $value      = 0;
        foreach ($neighbours as $neighbourPosition) {
            $neighbour = $this->getCell(...$neighbourPosition);
            if ($neighbour->isMined()) {
                $value++;
            }
        }
        $cell->setValue($value);
        return $cell;
    }

    private function getNeighbours(int $row, int $column): array
    {
        $neighbours = [
            [$row - 1, $column],
            [$row + 1, $column],
            [$row, $column + 1],
            [$row, $column - 1],
            [$row - 1, $column + 1],
            [$row - 1, $column - 1],
            [$row + 1, $column + 1],
            [$row + 1, $column - 1],
        ];

        return array_filter($neighbours, function (array $position) {
            if ($position[0] < 0 || $position[1] < 0) {
                return false;
            }
            if ($position[0] >= $this->size || $position[1] >= $this->size) {
                return false;
            }
            return true;
        });
    }

    public function getCell(int $row, int $column): Cell
    {
        return $this->cells[$row][$column];
    }

    public function clickCell(Cell $cell): void
    {
        $cell->click();
        $this->computeCellValue($cell);
        $this->updateCell($cell);

        if ($cell->getValue()) {
            return;
        }

        $neighbours = $this->getNeighbours(...$cell->getPosition());
        foreach ($neighbours as $neighbourPosition) {
            $neighbour = $this->getCell(...$neighbourPosition);
            if ($neighbour->isMined() || $neighbour->isClicked()) {
                continue;
            }

            $this->clickCell($neighbour);
        }
    }

    public function flagCell(Cell $cell, bool $flagged): void
    {
        $cell->setFlagged($flagged);
        $this->updateCell($cell);
    }

    private function updateCell(Cell $cell): void
    {
        $position                          = $cell->getPosition();
        $cells                             = $this->cells;
        $cells[$position[0]][$position[1]] = $cell;
        $this->cells                       = $cells;
    }
}
