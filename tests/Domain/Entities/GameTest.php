<?php

namespace Tests\Domain\Entities;

use Domain\Entities\Game;
use Tests\TestCase;

class GameTest extends TestCase
{
    public function testExternalReferenceableTrait(): void
    {
        $sut = Game::create(4, 5);

    }
}
