<?php

namespace ViewComponents\Core\Block;

use ViewComponents\Core\AbstractContainer;

class Container extends AbstractContainer
{
    public function __construct(array $innerBlocks = [])
    {
        $this->addInnerBlocks($innerBlocks);
    }
    protected function renderInternal()
    {
        return $this->renderInnerBlocks();
    }
}
