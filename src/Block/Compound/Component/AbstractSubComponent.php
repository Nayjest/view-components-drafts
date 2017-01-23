<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Exception;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\BlockInterface;

abstract class AbstractSubComponent implements ComponentInterface
{
    const EVENT_ID = 'attach_sub_component';

    private $isAttached = false;

    /**
     * @return ComponentInterface
     */
    abstract protected function getSubComponent();

    abstract protected function getParentSelector();

    /**
     * @param Compound $root
     * @return Compound|null
     */
    protected function findDestinationRoot(Compound $root)
    {
        return self::findBlockRecursive($this->getParentSelector(), $root);
    }

    /**
     * @param string $selector
     * @param Compound $root
     * @return null|BlockInterface
     */
    protected static function findBlockRecursive($selector, Compound $root)
    {
        $ids = explode('.', $selector);
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

    protected function attach(Compound $root)
    {
        if ($this->isAttached) {
            return;
        }
        $parent = $this->findDestinationRoot($root);
        if (!$parent) {
            $this->isAttached = true;
            $tries = 1;
            do {
                $tries++;
                if ($tries > 3) {
                    $this->isAttached = false;
                    throw new Exception("Can't find parent for subComponent");
                }
                $root->dispatchEvent(self::EVENT_ID);
                $parent = $this->findDestinationRoot($root);
            } while ($parent === null);
            $this->isAttached = false;
            if (!$parent instanceof Compound) {
                throw new Exception("Invalid parent for sub-component");
            }
            $parent->addComponent($this->getSubComponent());
            $this->isAttached = true;
        }
    }

    public function handle($eventId, Compound $root)
    {
        switch ($eventId) {
            case self::EVENT_ID:
                $this->attach($root);
                break;
            case Compound::EVENT_SET_ROOT:
                $root->defineEvent(self::EVENT_ID)->before(Compound::EVENT_INIT);
                break;
            case Compound::EVENT_UNSET_ROOT:
                if ($this->isAttached) {
                    $this->findDestinationRoot($root)->removeComponent($this->getSubComponent());
                    $this->isAttached = false;
                }
                break;
        }
    }
}
