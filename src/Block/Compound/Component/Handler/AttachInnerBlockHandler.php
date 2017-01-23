<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Exception;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\ContainerInterface;

/**
 * Trait for components that represents internal blocks inside compound block.
 * @see BlockComponentInterface
 *
 * Usage:
 * 1) Add this trait to class that must implement BlockComponentInterface
 * 2) Call $this->parsePath($path) in constructor or implement `protected getPath() : string`
 * 3) Implement public function getBlock()
 *
 */
class AttachInnerBlockHandler
{
    /** @var BlockInterface */
    private $block;
    /** @var BlockInterface */
    private $realInnerBlock;
    /** @var BlockInterface */
    private $parentSelector = false;
    /** @var BlockInterface[] */
    private $toRemove = [];

    public function __construct($parentSelector, BlockInterface $block)
    {
        $this->setParentSelector($parentSelector);
        $this->setBlock($block);
    }

    public function __invoke($eventId, Compound $root)
    {
        switch ($eventId) {
            case Compound::EVENT_SET_ROOT:
                if ($this->block instanceof Compound) {
                    $root->shareEventSequenceTo($this->block);
                }
                return; // exit to avoid dispatching non-broadcasting events by $block
            case Compound::EVENT_UNSET_ROOT:
                $this->markCurrentForRemoval()->cleanup($root);
                return; // exit to avoid dispatching non-broadcasting events by $block
            case Compound::EVENT_ATTACH_INNER_BLOCKS:
                // If it's required to render root multiple times
                // and blocks are replaced between render calls,
                // it's required to remove blocks, attached in previous cycles.
                $this->cleanup($root);
                $container = $this->getContainer($root);
                if (!$container->hasInnerBlock($this->realInnerBlock)) {
                    $container->addInnerBlock($this->realInnerBlock);
                }
                break;
        }
        if ($this->block instanceof Compound) {
            $this->block->dispatchEvent($eventId);
        }
    }

    private function cleanup(Compound $root)
    {
        /** @var ContainerInterface[] $containers */
        $containers = [];
        foreach($this->toRemove as list($selector, $block)) {
            if (!array_key_exists($selector, $containers)) {
                $containers[$selector] = $this->getContainer($root);
            }
            if ($containers[$selector]->hasInnerBlock($block)) {
                $containers[$selector]->removeInnerBlock($block);
            }
        }
        $this->toRemove = [];
        return $this;
    }

    /**
     * @param Compound $root
     * @return ContainerInterface
     * @throws Exception
     */
    protected function getContainer(Compound $root)
    {
        $parent = $root->findBlock($this->parentSelector);
        // @todo: $parent instanceof Compound && !$parent instanceof ComponentInterface
        $container = $parent instanceof Compound ? $parent->getRootContainer() : $parent;
        if (!$container instanceof ContainerInterface) {
            throw new Exception("Invalid parent block");
        }
        return $container;
    }

    /**
     * @return BlockInterface
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param BlockInterface $block
     * @return $this
     */
    public function setBlock(BlockInterface $block)
    {
        if ($this->isInitialized() && $this->block !== $block) {
            $this->markCurrentForRemoval();
        }
        $this->block = $block;
        $this->realInnerBlock = $block instanceof Compound ? $block->getRootContainer() : $block;
        return $this;
    }

    private function markCurrentForRemoval()
    {
        $this->toRemove[] = [$this->parentSelector, $this->realInnerBlock];
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentSelector()
    {
        return $this->parentSelector;
    }

    /**
     * @param $parentSelector
     * @return $this
     */
    public function setParentSelector($parentSelector)
    {
        if ($this->isInitialized() && $this->parentSelector !== $parentSelector) {
            $this->markCurrentForRemoval();
        }
        $this->parentSelector = $parentSelector;
        return $this;
    }

    private function isInitialized()
    {
        return $this->block !== null && $this->parentSelector !== false;
    }
}
