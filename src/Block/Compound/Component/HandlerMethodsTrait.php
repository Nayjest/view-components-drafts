<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Nayjest\StrCaseConverter\Str;
use ViewComponents\Core\Block\Compound;

trait HandlerMethodsTrait
{
    private static $handlers = [];


    private function getHandlerMethod($eventId)
    {
        self::updateHandlers();
        return array_key_exists($eventId, static::$handlers[static::class])
            ? static::$handlers[static::class][$eventId]
            : null;
    }

    private static function updateHandlers()
    {
        if (!array_key_exists(static::class, self::$handlers)) {
            self::$handlers[static::class] = [];
            foreach (get_class_methods(static::class) as $method) {
                if (substr($method, 0, 6) === 'handle') {
                    self::$handlers[static::class][self::getEventId($method)] = $method;
                }
            }
        }
    }

    private static function getEventId($methodName)
    {
        return Str::toSnakeCase(
            str_replace(
                'Event',
                '',
                substr($methodName, 6))
        );
    }

    public final function handle($eventId, Compound $root)
    {
        $method = self::getHandlerMethod($eventId);
        if ($method !== null) {
            $this->$method($root);
        }
    }
}