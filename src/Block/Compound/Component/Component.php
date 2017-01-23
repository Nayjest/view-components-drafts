<?php

namespace ViewComponents\Core\Block\Compound\Component;

use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\Handler\AttachComponentHandler;
use ViewComponents\Core\BlockInterface;

class Component implements BlockProviderInterface
{
    /**
     * @var
     */
    private $id;
    /** @var AttachInnerBlockHandler[] */
    private $innerBlockHandlers = [];
    private $handlers = [];

    public static function make($id)
    {
        return new self($id);
    }

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function providesBlock($blockId)
    {
        return array_key_exists($blockId, $this->innerBlockHandlers);
    }

    public function getBlock($id)
    {
        return $this->innerBlockHandlers[$id]->getBlock();
    }

    public function setBlock($id, BlockInterface $block)
    {
        $this->innerBlockHandlers[$id]->setBlock($block);
        return $this;
    }

    public function moveBlock($id, $parentSelector)
    {
        $this->innerBlockHandlers[$id]->setParentSelector($parentSelector);
        return $this;
    }

    public function defineInnerBlock($path, BlockInterface $block)
    {
        list($blockId, $parentSelector) = self::parsePath($path);
        $this->handlers[] = $this->innerBlockHandlers[$blockId] = new AttachInnerBlockHandler($parentSelector, $block);
        return $this;
    }

    public function defineComponent($parentSelector, ComponentInterface $component)
    {
        $this->handlers[] = new AttachComponentHandler($parentSelector, $component);
        return $this;
    }

    public function defineHandler(callable $handler)
    {
        $this->handlers[] = $handler;
    }

    public function handle($eventId, Compound $root)
    {
        foreach($this->handlers as $handler) {
            call_user_func($handler, $eventId, $root);
        }
    }

    private static function parsePath($path)
    {
        $parts = explode(Compound::PATH_SEPARATOR, $path);
        return [array_pop($parts), join(Compound::PATH_SEPARATOR, $parts)];
    }

}