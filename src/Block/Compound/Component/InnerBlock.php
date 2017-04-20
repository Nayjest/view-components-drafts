<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Nayjest\DI\Definition\Item;
use Nayjest\DI\Definition\Relation;
use Nayjest\DI\HubInterface;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\ContainerInterface;

/**
 * Class InnerBlock
 *
 * 1) Adds target block into compound block hierarchy as inner block
 * 2) Registers <id>Block in hub
 */
class InnerBlock implements ComponentInterface
{
    public static function getFullId($blockId)
    {
        return $blockId . 'Block';
    }
    /**
     * @var
     */
    private $blockId;

    /**
     * @var
     */
    private $parentSelector;

    /**
     * @var BlockInterface
     */
    private $block;
    /** @var  HubInterface */
    private $hub;

    public function __construct($path, BlockInterface $block = null)
    {
        $this->parsePath($path);
        $this->block = $block;
    }

    public function register(HubInterface $hub)
    {
        $this->hub = $hub;
        $hub->addDefinitions([
            new Item(
                $id     = $this->blockId,
                $value  = $this->block
            ),
            new Relation(
                $target =  $this->parentSelector,
                $source =  $this->blockId,
                $handler = static::attachInnerBlockFunc())
        ]);
    }

    public function setBlock(BlockInterface $block)
    {
        $this->block = $block;
        if ($this->hub) {
            $this->hub->set($this->blockId, $block);
        }
        return $this;
    }

    /**
     * @return BlockInterface
     */
    public function getBlock()
    {
        return $this->block;
    }

    public static function attachInnerBlockFunc()
    {
        return function (ContainerInterface $container, BlockInterface $block, $oldVal) {
            //\dump("attach", $container, $block);
            if ($oldVal !== null && $oldVal !== $block) {
                $container->removeInnerBlock($oldVal);
            }
            if (!$container->hasInnerBlock($block)) {
                $container->addInnerBlock($block);
            }
        };
    }

    protected function parsePath($path)
    {
        $parts = explode('.', $path);
        foreach ($parts as &$part) {
            $part = self::getFullId($part);
        }
        $this->blockId = array_pop($parts);
        $this->parentSelector = join('.', $parts) ?: Compound::CONTAINER_BLOCK;
    }
}
