<?php

namespace ViewComponents\Core\Block\Compound\Component\InnerBlock;

use Nayjest\DI\Definition\Relation;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\ContainerInterface;

class InnerBlockRelation extends Relation
{
    private static $handlerFunction;

    /**
     * @return callable
     */
    public static function getHandler()
    {
        if (self::$handlerFunction === null) {
            self::$handlerFunction = function (ContainerInterface $container, BlockInterface $block = null, $oldVal) {
                //\dump("attach", $container, $block);
                if ($oldVal !== null && $oldVal !== $block) {
                    $container->removeInnerBlock($oldVal);
                }
                if ($block !== null && !$container->hasInnerBlock($block)) {
                    $container->addInnerBlock($block);
                }
            };
        }
        return self::$handlerFunction;
    }

    public static function getBlockId($blockId)
    {
        return strpos(ucfirst($blockId), 'Block') !== false ? $blockId : ($blockId . 'Block');
    }

    public function __construct($parentId, $id)
    {
        parent::__construct(
            self::getBlockId($parentId),
            self::getBlockId($id),
            $this->getHandler()
        );
    }
}