<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Exception;
use ViewComponents\Core\Block\Compound;

/**
 * Trait for blocks implementing BlockComponentInterface.
 * @see BlockComponentInterface
 *
 * Usage:
 * 1) Add this trait to block class that must implement BlockComponentInterface
 * 2) Implement abstract methods described in this trait
 *
 */
trait ComponentFeaturesTrait
{
    use BlockComponentTrait {
        BlockComponentTrait::handle as private handleInternal;
    }
    use HandlersTrait;

    /**
     * @return [<root class>, <path>]
     */
    abstract protected function getDestination();

    public function handle($eventId, Compound $root)
    {
        $this->checkRootType($eventId, $root, $this->getRequiredRootClass());
        $this->handleInternal($eventId, $root);
    }

    private function getRequiredRootClass()
    {
        return $this->getDestination()[0];
    }

    protected function getPath()
    {
        return $this->getDestination()[1];
    }

//    private function checkRootType(Compound $root)
//    {
//        $rootClass = $this->getRequiredRootClass();
//        if (!$root instanceof $rootClass) {
//            throw new Exception(
//                'Instance of '
//                . get_class($this)
//                . ' can not be used as component of '
//                . get_class($root)
//                . ', required root class: '
//                . $rootClass
//            );
//        }
//    }
}
