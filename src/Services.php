<?php

namespace ViewComponents\Core;

use ViewComponents\Core\Rendering\SimpleRenderer;
use ViewComponents\Core\Rendering\TemplateFinder;

class Services
{
    public static function renderer()
    {
        $renderer = new SimpleRenderer(new TemplateFinder([
            __DIR__ . '/../resources/views'
        ]));
        return $renderer;
    }
}