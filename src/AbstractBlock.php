<?php

namespace ViewComponents\Core;

use ViewComponents\Core\Common\MakeTrait;

abstract class AbstractBlock implements BlockInterface
{
    use MakeTrait;
    use BlockTrait;
}
