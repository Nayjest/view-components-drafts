<?php

namespace ViewComponents\Core;

interface ContainerInterface extends BlockInterface
{
    /**
     * @return BlockInterface[]
     */
    public function getInnerBlocks();

    /**
     * @return BlockInterface[]
     */
    public function getInnerBlocksRecursive();
    public function addInnerBlock(BlockInterface $childBlock);
    public function removeInnerBlock(BlockInterface $childBlock);
    public function hasInnerBlock(BlockInterface $childNode);
    public function addInnerBlocks(array $blocks);
}
