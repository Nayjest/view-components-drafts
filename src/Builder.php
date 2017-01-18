<?php

namespace ViewComponents\Core;

use InvalidArgumentException;
use Nayjest\StrCaseConverter\Str;
use ReflectionClass;

class Builder
{
    private $className;
    private $constructorArguments = [];
    private $properties = [];

    public function __construct($classNameOrConfig = null, array $config = null)
    {
        if (is_string($classNameOrConfig)) {
            $this->className = $classNameOrConfig;
        } elseif (is_array($classNameOrConfig)) {
            $this->configure($classNameOrConfig);
        } else {
            throw new InvalidArgumentException;
        }
        if ($config) {
            $this->configure($config);
        }
    }

    public static function make($classNameOrConfig = null)
    {
        return new static($classNameOrConfig);
    }

    /**
     * @param mixed $constructorArguments
     * @return Builder
     */
    public function arguments(array $constructorArguments)
    {
        $this->constructorArguments = $constructorArguments;
        return $this;
    }


    public function set($nameOrArray, $value = null)
    {
        if (is_array($nameOrArray)) {
            $this->properties = array_merge($this->properties, $nameOrArray);
        } elseif (is_string($nameOrArray)) {
            $this->properties[$nameOrArray] = $value;
        } else {
            throw new InvalidArgumentException;
        }
        return $this;
    }

    private function instantiate()
    {
        $arguments = $this->constructorArguments;
        $class = $this->className;
        switch (count($arguments)) {
            case 0:
                return new $class();
            case 1:
                return new $class(array_shift($arguments));
            case 2:
                return new $class(
                    array_shift($arguments),
                    array_shift($arguments)
                );
            case 3:
                return new $class(
                    array_shift($arguments),
                    array_shift($arguments),
                    array_shift($arguments)
                );
        }
        $reflection = new ReflectionClass($class);
        return $reflection->newInstanceArgs($arguments);
    }

    public function build($className = null)
    {
        if ($className) {
            $this->className = $className;
        }
        $instance = $this->instantiate();
        foreach ($this->properties as $name => $value) {
            $setter = 'set' . ucfirst(Str::toCamelCase($name));
            call_user_func([$instance, $setter], $value);
        }
        return $instance;
    }

    public function __invoke()
    {
        return $this->build();
    }

    public function configure(array $config)
    {
        if (isset($config['@class'])) {
            $this->className = $config['@class'];
            unset($config['@class']);
        }
        if (isset($config['@argumentNames'])) {
            if (!isset($config['@arguments'])) {
                $config['@arguments'] = [];
            }
            foreach($config['@argumentNames'] as $name) {
                if (isset($config[$name])) {
                    $config['@arguments'][$name] = $config[$name];
                    unset($config[$name]);
                } else {
                    $config['@arguments'][$name] = null;
                }
            }
            unset($config['@argumentNames']);
        }
        if (isset($config['@arguments'])) {
            $this->constructorArguments = $config['@arguments'];
            unset($config['@arguments']);
        }

        $this->properties = $config;
        return $this;
    }

}