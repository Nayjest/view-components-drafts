<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Exception;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\ContainerInterface;
use ViewComponents\Core\Exception\InvalidRootException;

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
    protected $id;

    protected $parentId = false;

    private $blockIsSubComponent = false;

    public function handle($eventId, Compound $root)
    {
        /** @var BlockInterface|Compound|BlockComponentInterface $block */
        $block = $this->getBlock();
        switch ($eventId) {
            case Compound::EVENT_SET_ROOT:
                if ($block instanceof Compound) {
                    $root->shareEventSequenceTo($block);
                }
                return; // exit to avoid dispatching non-broadcasting events by $block
            case Compound::EVENT_UNSET_ROOT:
                if ($this->blockIsSubComponent) {
                    $this->findRootForSubComponent($block, $root)->removeComponent($block->getId());
                } else {
                    $this->findContainer($root)->removeInnerBlock($block);
                }
                return; // exit to avoid dispatching non-broadcasting events by $block
            case Compound::EVENT_ATTACH_SUB_COMPONENTS:
                $this->tryAttachBlockAsSubComponent($block, $root);
                break;
            case Compound::EVENT_ATTACH_INNER_BLOCKS:
                if (!$this->blockIsSubComponent) {
                    $this->attachInnerBlock($block, $root);
                }
                break;
        }
        if ($block instanceof Compound && !$this->blockIsSubComponent) {
            $block->dispatchEvent($eventId);
        }
    }

    private function tryAttachBlockAsSubComponent(BlockInterface $block, Compound $root)
    {
        /** @var BlockComponentInterface|BlockInterface|Compound $block */
        $otherRoot = $this->findRootForSubComponent($block, $root);
        if ($otherRoot !== null) {
            try {
                $otherRoot->addComponent($block);
                $this->blockIsSubComponent = true;
                // other root may share it's event sequence to block
                // and we need to restore event sequence of top root
                if ($block instanceof Compound) {
                    $root->shareEventSequenceTo($block);
                }
            } catch (InvalidRootException $e) {
            }
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
    protected function attachInnerBlock(BlockInterface $block, Compound $root)
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
        $block = $this->findParentBlock($root);
        return $block instanceof ContainerInterface ? $block : null;
    }

    /**
     * @param Compound $root
     * @return BlockInterface
     */
    private function findParentBlock(Compound $root)
    {
        $parentId = $this->getParentId();
        return $parentId !== null ? $root->getBlock($parentId) : $root->getRootContainer();
    }

    /**
     * @param BlockInterface $block
     * @param Compound $root
     * @return null|Compound
     */
    private function findRootForSubComponent(BlockInterface $block, Compound $root)
    {
        if (!$block instanceof ComponentInterface || $block === $this) {
            return null;
        }
        $parentBlock = $this->findParentBlock($root);
        return $parentBlock instanceof Compound ? $parentBlock : null;
    }
}
