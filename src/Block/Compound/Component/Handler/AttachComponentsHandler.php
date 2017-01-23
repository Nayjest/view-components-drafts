<?php

namespace ViewComponents\Core\Block\Compound\Component\Handler;

use Exception;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;

class AttachComponentsHandler
{
    const EVENT_ID = 'attach_sub_component';

    const SELECTOR = 0;
    const COMPONENT = 1;
    const IS_ATTACHED = 2;

    /** @var ComponentInterface[] */
    private $components = [];
    private $selectors = [];
    private $isAttached = [];
    private $isLocked = false;

    public function register($rootSelector, ComponentInterface $component)
    {
        if ($this->isLocked) {
            throw new Exception("Impossible to register Component when event handling started.");
        }
        $this->selectors[] = $rootSelector;
        $this->components[] = $component;
        $this->isAttached[] = false;
        return $this;
    }

    protected function attach(Compound $root, $index)
    {
        if ($this->isAttached[$index]) {
            return;
        }
        /** @var Compound $parent */
        $parent = $root->findBlock($this->selectors[$index]);
        $tries = 1;
        while ($parent === null) {
            $tries++;
            if ($tries > 3) {
                throw new Exception("Can't find parent for subComponent");
            }
            $this->isAttached[$index] = true;
            $root->dispatchEvent(self::EVENT_ID);
            $this->isAttached[$index] = false;
            $parent = $root->findBlock($this->selectors[$index]);
        };
        if (!$parent instanceof Compound) {
            throw new Exception("Invalid parent for sub-component");
        }
        $parent->addComponent($this->components[$index]);
        $this->isAttached[$index] = true;
    }

    public function handle($eventId, Compound $root)
    {
        switch ($eventId) {
            case self::EVENT_ID:
                $this->isLocked = true;
                foreach (array_keys($this->components) as $index) {
                    $this->attach($root, $index);
                }
                break;
            case Compound::EVENT_SET_ROOT:
                $root->defineEvent(self::EVENT_ID)->before(Compound::EVENT_INIT);
                break;
            case Compound::EVENT_UNSET_ROOT:
                foreach (array_keys($this->components) as $index) {
                    if ($this->isAttached[$index]) {
                        /** @var Compound $parent */
                        $parent = $root->findBlock($this->selectors[$index]);
                        $parent->removeComponent($this->components[$index]->getId());
                        $this->isAttached[$index] = false;
                    }
                }
                break;
        }
    }
}
