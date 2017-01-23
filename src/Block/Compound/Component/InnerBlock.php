<?php

namespace ViewComponents\Core\Block\Compound\Component;

use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\BlockInterface;

class InnerBlock implements BlockComponentInterface
{

    private $id;
    /**
     * @var callable
     */
    private $handler;

    private $attachHandler;

    /**
     * Component constructor.
     * @param $path
     * @param BlockInterface $block
     * @param callable|null $handler
     */
    public function __construct($path, BlockInterface $block, callable $handler = null)
    {
        list($this->id, $parentSelector) = $this->parsePath($path);
        $this->attachHandler = new AttachInnerBlockHandler($parentSelector, $block);
        $this->handler = $handler;
    }

    public function providesBlock($blockId)
    {
        return $this->id === $blockId;
    }


    protected function parsePath($path)
    {
        $parts = explode(Compound::PATH_SEPARATOR, $path);
        return [array_pop($parts), join(Compound::PATH_SEPARATOR, $parts)];
    }

    /**
     * @return BlockInterface
     */
    public function getBlock()
    {
        return $this->attachHandler->getBlock();
    }

    /**
     * @param BlockInterface $block
     * @return $this
     */
    public function setBlock(BlockInterface $block)
    {
        $this->attachHandler->setBlock($block);
        return $this;
    }

    public function handle($eventId, Compound $root)
    {
        $this->attachHandler->__invoke($eventId, $root);
        if ($this->handler !== null && $eventId === Compound::EVENT_FINALIZE) {
            call_user_func($this->handler, $root);
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function moveTo($parentSelector)
    {
        $this->attachHandler->setParentSelector($parentSelector);
        return $this;
    }
}
