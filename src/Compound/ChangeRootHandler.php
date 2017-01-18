<?php

namespace ViewComponents\Core\Compound;

class ChangeRootHandler implements HandlerInterface
{
    /**
     * @var ComponentInterface
     */
    private $component;
    /**
     * @var callable
     */
    private $rootResolver;


    public function __construct(ComponentInterface $component, callable $rootResolver)
    {
        $this->component = $component;
        $this->rootResolver = $rootResolver;
    }

    public function execute(CompoundBlockInterface $root)
    {
        $handler = function(CompoundBlockInterface $root) {
            $newRoot = call_user_func($this->rootResolver, $root);
            if (!($newRoot instanceof Compound)) {
                throw new \Exception("Can't resolve root component");
            }
            if ($newRoot !== $root) {
                $newRoot->addComponent($this->component);
            }
        };
        $handler($root);
    }

    /**
     * @return int|float
     */
    public function getEventId()
    {
        return Handler::PRIORITY_ADD_SUB_COMPONENTS;
    }
}