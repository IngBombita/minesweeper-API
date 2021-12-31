<?php

namespace Domain\Entities;

class Box
{
    public function __construct(
      private bool $mined,
      private bool $clicked = false,
      private bool $flagged = false,
      private ?int $value = null,
    ) {}

    public function isMined(): bool
    {
        return $this->mined;
    }

    public function isClicked(): bool
    {
        return $this->clicked;
    }

    public function isFlagged(): bool
    {
        return $this->flagged;
    }
}
