<?php

namespace ViewComponents\Core\Block\Compound\Component;

use ViewComponents\Core\Block\Compound;

class Event implements ComponentInterface
{

    private $tail;
    private $head;
    private $eventId;
    /**
     * @var callable
     */
    private $handler;

    /**
     * @param string $eventId
     * @param callable|null $handler
     * @return static
     */
    public static function make($eventId, callable $handler = null)
    {
        return new static($eventId, $handler);
    }

    /**
     * Event constructor.
     * @param string $eventId
     * @param callable|null $handler
     */
    public function __construct($eventId, callable $handler = null)
    {
        $this->eventId = $eventId;
        $this->handler = $handler;
    }

    public function before($id)
    {
        if (is_array($id)) {
            foreach ($id as $i) {
                $this->before($i);
            }
        } else {
            $this->tail[] = $id;
        }

        return $this;
    }

    public function after($id)
    {
        if (is_array($id)) {
            foreach ($id as $i) {
                $this->after($i);
            }
        } else {
            $this->head[] = $id;
        }
        return $this;
    }

    public function getId()
    {
        return 'event_' . $this->eventId;
    }

    public function handle($eventId, Compound $root)
    {
        if ($eventId === Compound::EVENT_SET_ROOT) {
            $root
                ->defineEvent($this->eventId)
                ->after($this->head)
                ->before($this->tail);
        }
        if ($this->handler !== null && $eventId === $this->eventId) {
            call_user_func($this->handler, $root);
        }
    }
}
