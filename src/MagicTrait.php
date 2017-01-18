<?php

namespace ViewComponents\Core;

use Exception;
use Nayjest\StrCaseConverter\Str;

trait MagicTrait
{
    protected static $magicPrefixes = ['get', 'set', 'has'];

    private function magic()
    {
        $arguments = func_get_args();
        $prefix = array_shift($arguments);
        $name = array_shift($arguments);
        if (!in_array($prefix, static::$magicPrefixes)) {
            $className = get_class($this);
            throw new Exception("Invalid method call $className::$prefix.$name");
        }
        foreach (static::$magicSuffixes as $suffix) {
            if (strpos($name, $suffix) !== false) {
                $value = Str::toSnakeCase(str_replace($suffix, '', $name));
                array_unshift($arguments, $value);
                $method = $prefix . $suffix;
                return call_user_func_array([$this, $method], $arguments);
            }
        }
        throw new Exception("Property $name does not exists.");
    }

    public function __get($name)
    {
        return $this->magic('get', $name);
    }

    public function __set($name, $value)
    {
        return $this->magic('set', $name, $value);
    }

    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        $name = substr($name, 3);
        $magicArguments = array_merge([$prefix, $name], $arguments);
        return call_user_func_array([$this, 'magic'], $magicArguments);
    }
}