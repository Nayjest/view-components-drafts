<?php

namespace ViewComponents\Core\Block\Compound\Component;

use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Exception\InvalidRootException;

trait HandlersTrait
{
    abstract public function getId();

    protected function checkRootType($eventId, Compound $root, $expectedRootClass)
    {
        if ($eventId === Compound::EVENT_SET_ROOT) {
            if (!$root instanceof $expectedRootClass) {
                $componentClass = static::class;
                $rootClass = get_class($root);
                $component = "instance of $componentClass (id = {$this->getId()})";
                throw new InvalidRootException(
                    "Can't use $rootClass as root for  $component, it expects instance of $expectedRootClass as root"
                );

            }
        }
        return $this;
    }
}