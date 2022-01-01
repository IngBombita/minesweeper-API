<?php

namespace Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class Cell extends Model
{
    protected $hidden = ['mined'];

    public static function create(
        bool  $mined,
        array $position
    ): self {
        $cell           = new self();
        $cell->mined    = $mined;
        $cell->position = $position;
        $cell->clicked  = false;
        $cell->flagged  = false;
        $cell->value    = null;

        $cell->makeVisibleIf($cell->clicked, 'mined');

        return $cell;
    }

    public function serialize(): array
    {
        return [
            'mined'    => $this->mined,
            'position' => $this->position,
            'clicked'  => $this->clicked,
            'flagged'  => $this->flagged,
            'value'    => $this->value,
        ];
    }

    public static function unserialize(array $props): self
    {
        $cell           = new self();
        $cell->mined    = $props['mined'];
        $cell->position = $props['position'];
        $cell->clicked  = $props['clicked'];
        $cell->flagged  = $props['flagged'];
        $cell->value    = $props['value'];

        $cell->makeVisibleIf($cell->clicked, 'mined');

        return $cell;
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
