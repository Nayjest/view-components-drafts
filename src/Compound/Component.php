<?php

namespace ViewComponents\Core\Compound;

use ViewComponents\Core\BlockInterface;

class Component extends AbstractBlockComponent
{
    private $id;
    private $parentId;
    private $block;
    /**
     * @var array
     */
    private $handlers;

    /**
     * Component constructor.
     * @param $path
     * @param BlockInterface $block
     * @param HandlerInterface[]|Handler|callable $handlerOrHandlers
     */
    public function __construct($path, BlockInterface $block, $handlerOrHandlers = [])
    {
        $parts = explode('.', $path);
        $this->id = array_pop($parts);
        $this->parentId = count($parts) ? array_pop($parts) : null;
        $this->block = $block;

        if (is_array($handlerOrHandlers)) {
            $this->handlers = $handlerOrHandlers;
        } elseif(is_callable($handlerOrHandlers)) {
            $this->handlers = [new Handler(Handler::PRIORITY_PREPARE_VIEW, $handlerOrHandlers)];
        } elseif ($handlerOrHandlers instanceof HandlerInterface) {
            $this->handlers = [$handlerOrHandlers];
        } elseif ($handlerOrHandlers !== null) {
            throw new \InvalidArgumentException("Wrong handler.");
        }
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getParentId()
    {
        return $this->parentId;
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

    /**
     * @param mixed|null $parentId
     * @return Component
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    public function getEventHandlers()
    {
        return array_merge(parent::getEventHandlers(), $this->handlers);
    }

    /**
     * @param HandlerInterface[] $handlers
     * @return $this
     */
    public function setHandlers(array $handlers)
    {
        $this->handlers = $handlers;
        return $this;
    }

    public function addHandler(HandlerInterface $handler)
    {
        $this->handlers[] = $handler;
        return $this;
    }
}
