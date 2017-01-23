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
class AttachInnerBlocksHandler
{
    /** @var BlockInterface[] */
    private $blocks = [];
    private $selectors = [];
    private $isAttached = [];

    public function register($containerSelector, BlockInterface $block)
    {
        $this->selectors[] = $containerSelector;
        $this->blocks[] = $block;
        $this->isAttached[] = false;
        return $this;
    }

    public function handle($eventId, Compound $root)
    {
        switch ($eventId) {
            case Compound::EVENT_SET_ROOT:
                foreach ($this->blocks as $block) {
                    if ($block instanceof Compound) {
                        $root->shareEventSequenceTo($block);
                    }
                }
                return; // exit to avoid dispatching non-broadcasting events by $block

            case Compound::EVENT_UNSET_ROOT:
                foreach ($this->blocks as $index => $block) {
                    if (!$this->isAttached[$index]) {
                        continue;
                    }
                    $this
                        ->getContainer($index, $root)
                        ->removeInnerBlock($block instanceof Compound ? $block->getRootContainer() : $block);
                    $this->isAttached[$index] = false;
                }
                return; // exit to avoid dispatching non-broadcasting events by $block

            case Compound::EVENT_ATTACH_INNER_BLOCKS:
                foreach ($this->blocks as $index => $block) {
                    if ($this->isAttached[$index]) {
                        continue;
                    }
                    $this
                        ->getContainer($index, $root)
                        ->addInnerBlock($block instanceof Compound ? $block->getRootContainer() : $block);
                    $this->isAttached[$index] = true;
                }
                break;
        }
        foreach ($this->blocks as $index => $block) {
            if ($block instanceof Compound) {
                $block->dispatchEvent($eventId);
            }
        }
    }

    /**
     * @param Compound $root
     * @param $index
     * @return ContainerInterface
     * @throws Exception
     */
    protected function getContainer($index, Compound $root)
    {
        $parent = $root->findBlock($this->selectors[$index]);
        // @todo: $parent instanceof Compound && !$parent instanceof ComponentInterface
        $container = $parent instanceof Compound ? $parent->getRootContainer() : $parent;
        if (!$container instanceof ContainerInterface) {
            throw new Exception("Invalid parent block");
        }
        return $container;
    }
}
