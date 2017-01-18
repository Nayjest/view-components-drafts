<?php

namespace ViewComponents\Core\Compound;



class CheckTypeHandler implements HandlerInterface
{
    /**
     * @var string
     */
    private $rootClass;

    public function __construct($rootClass)
    {
        $this->rootClass = $rootClass;
    }

    public function getEventId()
    {
        // TODO: Implement getPriority() method.
    }
}