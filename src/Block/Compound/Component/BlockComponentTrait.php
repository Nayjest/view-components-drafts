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
trait BlockComponentTrait
{
    private $id;

    protected $parentId = false;

    public function handle($eventId, Compound $root)
    {
        /** @var BlockInterface|Compound $block */
        /** @var  BlockComponentInterface|static $this */
        $block = $this->getBlock();
        switch ($eventId) {
            case Compound::EVENT_SET_ROOT:
                if ($this->getBlock() instanceof Compound) {
                    $root->shareEventSequenceTo($this->getBlock());
                }
                return; // exit to avoid dispatching non-broadcasting events by $block

            case Compound::EVENT_UNSET_ROOT:
                $this->findContainer($root)->removeInnerBlock($this->getBlock());
                return; // exit to avoid dispatching non-broadcasting events by $block
            case Compound::EVENT_ATTACH_INNER_BLOCKS:
                $this->attachToParent($block, $root);
                break;
        }
        if ($block instanceof Compound) {
            $block->dispatchEvent($eventId);
        }
    }

    public function getId()
    {
        if ($this->id === null && method_exists($this, 'getPath')) {
            $this->parsePath($this->getPath());
        }
        return $this->id;
    }

    /**
     * @param string|null $parentId
     * @return $this
     */
    public function moveTo($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * @return string|null
     */
    protected function getParentId()
    {
        if ($this->parentId === false && method_exists($this, 'getPath')) {
            $this->parsePath($this->getPath());
        }
        return $this->parentId;
    }

    /**
     * @param string $path
     */
    protected function parsePath($path)
    {
        $parts = explode('.', $path);
        $this->id = array_pop($parts);
        $this->parentId = count($parts) ? array_pop($parts) : null;
    }

    /**
     * Attaches block to it's parent container as inner block.
     *
     * @param BlockInterface $block
     * @param Compound $root
     * @throws Exception
     */
    protected function attachToParent(BlockInterface $block, Compound $root)
    {
        // Find container or throw Exception
        $container = $this->findContainer($root);
        if ($container === null) {
            $rootClass = get_class($root);
            throw new Exception(
                "Can not add '{$this->getId()}' into '{$this->getParentId()}' as inner block, container not found in $rootClass."
            );
        }

        // Replace inner block to its rootContainer to avoid dispatching events when rendering it
        // because this component already proxies events dispatching from root to $block.
        $innerBlock = $block instanceof Compound ? $block->getRootContainer() : $block;

        $container->hasInnerBlock($innerBlock) || $container->addInnerBlock($innerBlock);
    }

    /**
     * @param Compound $root
     * @return ContainerInterface|null
     */
    protected function findContainer(Compound $root)
    {
        $id = $this->getParentId();
        if ($id === null) {
            return $root->getRootContainer();
        }
        $block = $root->getBlock($id);
        return $block instanceof ContainerInterface ? $block : null;
    }
}
