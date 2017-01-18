<?php

namespace ViewComponents\Core\Block\Compound\Component;

use ViewComponents\Core\Block\Compound;

class Handler implements ComponentInterface
{
    private $id;
    private $handler;
    private $eventId;

    private $componentId;

    public function __construct($eventId, callable $handler, $id = null)
    {
        $this->handler = $handler;
        $this->eventId = $eventId;
        $this->componentId = $id;
    }

    public final function getId()
    {
        if ($this->id === null) {
            static $idAutoincrement = 1;
            $this->id = $this->eventId . '_event_handler_' . $idAutoincrement;
            $idAutoincrement++;
        }
        return $this->id;
    }

    public final function handle($eventId, Compound $root)
    {
        if ($eventId === $this->eventId) {
            call_user_func($this->handler, $root);
        }
    }
}
