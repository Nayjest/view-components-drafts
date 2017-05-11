<?php

namespace ViewComponents\Core\Block;

use Closure;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\BlockTrait;
use ViewComponents\Core\Common\MakeTrait;
use ViewComponents\Core\DataPresenterInterface;
use ViewComponents\Core\DataPresenterTrait;

class Block implements BlockInterface, DataPresenterInterface
{
    use BlockTrait;
    use MakeTrait;
    use DataPresenterTrait;

    public function __construct($data = null)
    {
        $this->setData($data);
    }

    protected function renderInternal()
    {
        return (string)($this->data instanceof Closure ? call_user_func($this->data) : $this->data);
    }
}
