<?php

namespace Domain\Enums;

use MyCLabs\Enum\Enum;

class CellActions extends Enum
{
    public const FLAG   = 'flag';
    public const UNFLAG = 'unflag';
    public const CLICK = 'click';
}
