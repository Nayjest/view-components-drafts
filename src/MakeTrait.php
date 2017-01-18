<?php

namespace ViewComponents\Core;

use ReflectionClass;

trait MakeTrait
{
    /**
     * @return static
     */
    public static function make()
    {
        $arguments = func_get_args();
        switch (count($arguments)) {
            case 0:
                return new static();
            case 1:
                return new static(array_shift($arguments));
            case 2:
                return new static(
                    array_shift($arguments),
                    array_shift($arguments)
                );
            case 3:
                return new static(
                    array_shift($arguments),
                    array_shift($arguments),
                    array_shift($arguments)
                );
        }
        $reflection = new ReflectionClass(static::class);
        return $reflection->newInstanceArgs($arguments);
    }
}