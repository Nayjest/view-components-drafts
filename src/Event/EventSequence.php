<?php
namespace ViewComponents\Core\Event;

use Iterator;

class EventSequence implements Iterator
{
    /** @var EventDefinition[] */
    private $eventDefinitions = [];
    private $isSorted = false;
    private $iteratorPosition = 0;

    public function merge(EventSequence $sequence)
    {
        foreach($sequence->eventDefinitions as $definition) {
            if (!$this->has($definition->id)) {
                $this->eventDefinitions[] = $definition;
                $this->isSorted = false;
            }
        }
        return $this;
    }

    public function define($eventId)
    {
        $definition = null;
        foreach($this->eventDefinitions as $i) {
            if ($i->id === $eventId) {
                $definition = $i;
            }
        }
        if ($definition === null) {
            $definition = new EventDefinition($eventId);
            $this->eventDefinitions[] = $definition;
        }
        $this->isSorted = false;
        return new EventDefinitionBuilder($definition);
    }

    public function has($eventId)
    {
        foreach($this->eventDefinitions as $i) {
            if ($i->id === $eventId) {
                return true;
            }
        }
        return false;
    }

    public function rewind()
    {
        $this->iteratorPosition = 0;
        if (!$this->isSorted) {
            $this->sortDefinitions();
        }
    }

    public function current()
    {
        return $this->eventDefinitions[$this->iteratorPosition]->id;
    }

    public function next()
    {
        $this->iteratorPosition++;
        if (!$this->isSorted) {
            $this->sortDefinitions();
        }
    }

    public function valid()
    {
        return array_key_exists($this->iteratorPosition, $this->eventDefinitions);
    }

    public function key()
    {
        return $this->iteratorPosition;
    }

    private function swapDefinitions($index1, $index2)
    {
        $a = $this->eventDefinitions[$index1];
        $this->eventDefinitions[$index1] = $this->eventDefinitions[$index2];
        $this->eventDefinitions[$index2] = $a;
    }

    protected function sortDefinitions()
    {
        $qty = count($this->eventDefinitions);
        for ($i = 0; $i < $qty; $i++) {
            for ($j = $i + 1; $j < $qty; $j++) {
                if ($i === $j) {
                    continue;
                }
                if ($this->eventDefinitions[$i]->after($this->eventDefinitions[$j])) {
                    $this->swapDefinitions($i, $j);
                    $i--;
                    break;
                }
            }
        }
        $this->isSorted = true;
    }
}