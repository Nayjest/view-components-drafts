<?php

namespace ViewComponents\Core\Event;


class EventDefinition
{
    public $id;
    public $head = [];
    public $tail = [];
    public $index;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function before(EventDefinition $b)
    {
        return in_array($b->id, $this->tail) || in_array($this->id, $b->head);
    }

    public function after(EventDefinition $b)
    {
        return in_array($this->id, $b->tail) || in_array($b->id, $this->head);
    }
}