<?php

namespace Domain\Entities;

class Cell
{
    public function __construct(
        private bool  $mined,
        private array $position,
        private bool  $clicked = false,
        private bool  $flagged = false,
        private ?int  $value = null,
    ) {
    }

    public function isMined(): bool
    {
        return $this->mined;
    }

    public function isClicked(): bool
    {
        return $this->clicked;
    }

    public function click(): void
    {
        $this->clicked = true;
    }

    public function isFlagged(): bool
    {
        return $this->flagged;
    }

    public function setFlagged(bool $value): void
    {
        $this->flagged = $value;
    }

    public function getPosition(): array
    {
        return $this->position;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): void
    {
        $this->value = $value;
    }
}
