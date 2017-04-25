<?php

namespace ViewComponents\Core\Common;

use BadMethodCallException;
use Nayjest\DI\HubInterface;

/**
 * Trait MagicHubAccessTrait
 *
 * Requirements:
 * @property HubInterface $hub protected hub
 */
trait MagicHubAccessTrait
{
    public function __get($name)
    {
        return $this->hub->get($name);
    }

    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        $name = lcfirst(substr($name, 3));
        if ($prefix === 'get') {
            return $this->hub->get($name);
        } elseif ($prefix === 'set' && $this->hub->has($name)) {
            $this->hub->set($name, $arguments[0]);
            return $this;
        }
        throw new BadMethodCallException;
    }

    public function __set($name, $value)
    {
        $this->hub->set($name, $value);
    }
}
