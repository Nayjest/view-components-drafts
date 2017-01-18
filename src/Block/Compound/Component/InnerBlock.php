<?php

namespace ViewComponents\Core\Block\Compound\Component;

use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\BlockInterface;

class InnerBlock implements BlockComponentInterface
{
    use BlockComponentTrait {
        BlockComponentTrait::handle as internalHandle;
    }

    private $block;
    /**
     * @var callable
     */
    private $handler;

    /**
     * Component constructor.
     * @param $path
     * @param BlockInterface $block
     * @param callable|null $handler
     */
    public function __construct($path, BlockInterface $block, callable $handler = null)
    {
        $this->parsePath($path);
        $this->block = $block;
        $this->handler = $handler;
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
        $this->block = $block;
        return $this;
    }

    public function handle($eventId, Compound $root)
    {
        $this->internalHandle($eventId, $root);
        if ($this->handler !== null && $eventId === Compound::EVENT_FINALIZE) {
            call_user_func($this->handler, $root);
        }
    }
}
