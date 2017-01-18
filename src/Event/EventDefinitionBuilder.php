<?php

namespace ViewComponents\Core\Event;


class EventDefinitionBuilder
{
    /**
     * @var EventDefinition
     */
    private $eventDefinition;

    public function __construct(EventDefinition $eventDefinition)
    {
        $this->eventDefinition = $eventDefinition;
    }

    public function before($id)
    {
        if (is_array($id)) {
            foreach ($id as $i) {
                $this->before($i);
            }
        } elseif (!in_array($id, $this->eventDefinition->tail)) {
            $this->eventDefinition->tail[] = $id;
        }

        return $this;
    }

    public function after($id)
    {
        if (is_array($id)) {
            foreach ($id as $i) {
                $this->after($i);
            }
        } elseif (!in_array($id, $this->eventDefinition->head)) {
            $this->eventDefinition->head[] = $id;
        }
        return $this;
    }

    /**
     * @return EventDefinition
     */
    public function getEventDefinition()
    {
        return $this->eventDefinition;
    }
}