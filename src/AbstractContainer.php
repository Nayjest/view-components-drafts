<?php

namespace ViewComponents\Core;

use Exception;
use ViewComponents\Core\Common\MakeTrait;
use ViewComponents\Core\Common\StableSorting;

abstract class AbstractContainer implements BlockInterface, ContainerInterface
{
    use MakeTrait;
    use BlockTrait;

    private $blockSeparator = '';

    /**
     * @var BlockInterface[]
     */
    private $innerBlocks = [];

    /**
     * @return BlockInterface[]
     */
    protected function getInnerBlocksSorted()
    {
        $children = $this->getInnerBlocks();
        $children = StableSorting::sort($children, function (BlockInterface $a, BlockInterface $b) {
            $aPos = $a->getSortPosition() ?: 0;
            $bPos = $b->getSortPosition() ?: 0;
            if ($aPos == $bPos) {
                return 0;
            }
            return ($aPos < $bPos) ? -1 : 1;
        });
        return $children;
    }

    protected function renderInnerBlocks(array $childrenCollection = null)
    {
        $separator = $this->getBlockSeparator();
        $out = [];
        $children = $childrenCollection ?: $this->getInnerBlocksSorted();
        foreach ($children as $block) {
            $out[] = $block->render();
        }
        return count($out) ? $separator . join($separator, $out) . $separator : '';
    }

    public function getInnerBlocks()
    {
        return $this->innerBlocks;
    }

    /**
     * @return BlockInterface[]
     */
    public function getInnerBlocksRecursive()
    {
        $res = $this->getInnerBlocks();
        foreach ($this->getInnerBlocks() as $block) {
            if ($block instanceof ContainerInterface) {
                $res = array_merge($res, $block->getInnerBlocksRecursive());
            }
        }
        return $res;
    }

    public function addInnerBlock(BlockInterface $childBlock)
    {
        if ($this->hasInnerBlock($childBlock)) {
            throw new Exception("Trying to add child to block that is already it's parent.");
        }
        $this->innerBlocks[] = $childBlock;
        return $this;
    }

    public function addInnerBlocks(array $children)
    {
        foreach ($children as $child) {
            $this->addInnerBlock($child);
        }
        return $this;
    }

    public function removeInnerBlock(BlockInterface $childBlock)
    {
        if (($key = array_search($childBlock, $this->innerBlocks)) !== false) {
            unset($this->innerBlocks[$key]);
        }
        return $this;
    }

    public function hasInnerBlock(BlockInterface $childNode)
    {
        return in_array($childNode, $this->getInnerBlocks(), true);
    }

    /**
     * @return string
     */
    public function getBlockSeparator()
    {
        return $this->blockSeparator;
    }

    /**
     * @param string $blockSeparator
     * @return $this
     */
    public function setBlockSeparator($blockSeparator)
    {
        $this->blockSeparator = $blockSeparator;
        return $this;
    }
}
