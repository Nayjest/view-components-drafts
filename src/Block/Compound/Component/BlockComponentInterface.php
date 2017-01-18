<?php

namespace ViewComponents\Core\Block\Compound\Component;

use ViewComponents\Core\BlockInterface;

/**
 * Interface for component that adds block to compound.
 */
interface BlockComponentInterface extends ComponentInterface
{
    /**
     * @return BlockInterface
     */
    public function getBlock();
}