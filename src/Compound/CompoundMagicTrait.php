<?php

namespace ViewComponents\Core\Compound;

use ViewComponents\Core\MagicTrait;

trait CompoundMagicTrait
{
    use MagicTrait;
    protected static $magicSuffixes = ['Block', 'Component'];
}
