<?php

namespace Domain\Enums;

use MyCLabs\Enum\Enum;

class GameStatus extends Enum
{
    public const CREATED  = 'created';
    public const PLAYING  = 'playing';
    public const FINISHED = 'finished';
}
