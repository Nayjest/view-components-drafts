<?php

namespace ViewComponents\Core\Block;

use ViewComponents\Core\AbstractBlock;
use ViewComponents\Core\DataPresenterInterface;
use ViewComponents\Core\DataPresenterTrait;

class Block extends AbstractBlock implements DataPresenterInterface
{
    use DataPresenterTrait;

    public function __construct($data = null)
    {
        $this->setData($data);
    }

    protected function renderInternal()
    {
        return (string)$this->data;
    }
}
