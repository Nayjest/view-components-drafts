<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Nayjest\DI\Builder\DeferredDefinitionBuilder;
use ViewComponents\Core\Block\Compound\Component\InnerBlock\InnerBlockRelation;
use ViewComponents\Core\Common\MakeTrait;

/**
 * DI definition builder as compound component.
 */
class DefinitionBuilder extends DeferredDefinitionBuilder implements ComponentInterface
{
    use MakeTrait;

    public function usedByBlock($id, callable $func)
    {
        $this->usedBy(InnerBlockRelation::getBlockId($id), $func);
        return $this;
    }

    public function with($id)
    {
        $this->currentItemId = $id;
        return $this;
    }
    public function withBlock($id)
    {
        $this->currentItemId = InnerBlockRelation::getBlockId($id);
        return $this;
    }
}