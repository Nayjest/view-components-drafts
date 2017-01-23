<?php

namespace ViewComponents\Core\Block\Compound\Component\Handler;

use Exception;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;

class AttachComponentHandler
{
    const EVENT_ID = 'attach_sub_component';

    const SELECTOR = 0;
    const COMPONENT = 1;
    const IS_ATTACHED = 2;

    /** @var ComponentInterface */
    private $component;
    private $selector;
    private $isAttached = false;
    private $isLocked = false;

    public function __construct($parentSelector, ComponentInterface $component)
    {
        $this->selector = $parentSelector;
        $this->component = $component;
    }

    protected function attach(Compound $root)
    {
        if ($this->isAttached) {
            return;
        }
        /** @var Compound $parent */
        $parent = $root->findBlock($this->selector);
        $tries = 1;
        while ($parent === null) {
            $tries++;
            if ($tries > 3) {
                throw new Exception("Can't find parent for subComponent");
            }
            $this->isAttached = true;
            $root->dispatchEvent(self::EVENT_ID);
            $this->isAttached = false;
            $parent = $root->findBlock($this->selector);
        };
        if (!$parent instanceof Compound) {
            throw new Exception("Invalid parent for sub-component");
        }
        $parent->addComponent($this->component);
        $this->isAttached = true;
    }

    public function __invoke($eventId, Compound $root)
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
                        /** @var Compound $parent */
                        $parent = $root->findBlock($this->selector);
                        $parent->removeComponent($this->component->getId());
                        $this->isAttached = false;
                    }
                break;
        }
    }
}
