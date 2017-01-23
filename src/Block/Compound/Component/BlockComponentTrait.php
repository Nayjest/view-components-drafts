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
    abstract protected function getDestinationPath();

    private $isAttached = false;

    protected function getExpectedRootType()
    {
        return Compound::class;
    }

    /**
     * @param Compound $root
     * @return BlockInterface|null
     */
    protected function findParentBlock(Compound $root)
    {
        $ids = explode('.', $this->getDestinationPath());
        $id = array_pop($ids);
        if (count($ids) === 0) {
            return $root->getRootContainer();
        }
        $currentRoot = $root;
        $block = null;
        foreach ($ids as $parentId) {
            if (!$currentRoot->hasBlock($parentId)) {
                return null;
            }
            $block = $currentRoot->getBlock($parentId);
            if ($block instanceof Compound) {
                $currentRoot = $block;
            }
        }
        return $block;
    }

    public function handle($eventId, Compound $root)
    {
        /** @var self|BlockComponentInterface $this */
        $block = $this->getBlock();
        switch ($eventId) {
            case Compound::EVENT_SET_ROOT:
                if ($block instanceof Compound) {
                    $root->shareEventSequenceTo($block);
                }
                return; // exit to avoid dispatching non-broadcasting events by $block

            case Compound::EVENT_UNSET_ROOT:
                $this->tryDetachBlockFromRoot($root);
                return; // exit to avoid dispatching non-broadcasting events by $block

            case Compound::EVENT_ATTACH_INNER_BLOCKS:
                $this->tryAttachBlockToRoot($root);
                if (!$this->isAttached) {
                    $this->tryAttachParentToRoot($root);
                    $this->tryAttachBlockToRoot($root);
                    if (!$this->isAttached) {
                        $rootClass = get_class($root);
                        throw new Exception(
                            "Can not add '{$this->getId()}' into '{$this->getParentId()}' as inner block, "
                            . "container not found in $rootClass."
                        );
                    }
                }
                break;
        }
        if ($block instanceof Compound) {
            $block->dispatchEvent($eventId);
        }
    }

    protected function tryAttachParentToRoot(Compound $root)
    {
        $this->isAttached = true;
        $root->dispatchEvent(Compound::EVENT_ATTACH_INNER_BLOCKS);
        $this->isAttached = false;
    }

    protected function tryAttachBlockToRoot(Compound $root)
    {
        if ($this->isAttached) {
            return;
        }
        $parent = $this->findParentBlock($root);
        if ($parent !== null) {
            /** @var self|BlockComponentInterface $this */
            $block = $this->getBlock();
            $this->attachBlockToParent($block, $parent);
            $this->isAttached = true;
        }
    }

    protected function tryDetachBlockFromRoot(Compound $root)
    {
        if (!$this->isAttached) {
            return;
        }
        $parent = $this->findParentBlock($root);
        if ($parent !== null) {
            /** @var self|BlockComponentInterface $this */
            $block = $this->getBlock();
            $this->detachBlockFromParent($block, $parent);
            $this->isAttached = false;
        }
    }

    protected function attachBlockToParent(BlockInterface $block, BlockInterface $parent)
    {
        if ($parent instanceof Compound) {
            if ($block instanceof BlockComponentInterface) {
                $parent->addComponent($block);
                return;
            }
            $parent = $parent->getRootContainer();
        }
        if ($parent instanceof ContainerInterface) {
            $attachedBlock = $block instanceof Compound ? $block->getRootContainer() : $block;
            $parent->addInnerBlock($attachedBlock);
            return;
        }
        throw new Exception("Invalid parent block");

    }

    protected function detachBlockFromParent(BlockInterface $block, BlockInterface $parent)
    {
        if ($parent instanceof Compound) {
            if ($block instanceof ComponentInterface) {
                $parent->removeComponent($block->getId());
                return;
            }
            $parent = $parent->getRootContainer();
        }
        if ($parent instanceof ContainerInterface) {
            $attachedBlock = $block instanceof Compound ? $block->getRootContainer() : $block;
            $parent->removeInnerBlock($attachedBlock);
            return;
        }
        throw new Exception("Invalid parent block");
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
    public function moveTo($path)
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
