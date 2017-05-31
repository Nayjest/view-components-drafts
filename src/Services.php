<?php

namespace ViewComponents\Core;

use Nayjest\DI\Definition\Item;
use Nayjest\DI\Definition\Value;
use Nayjest\DI\Hub;

use ViewComponents\Core\Rendering\RendererInterface;
use ViewComponents\Core\Rendering\SimpleRenderer;
use ViewComponents\Core\Rendering\TemplateFinder;

class Services
{
    protected static $hub;

    /**
     * @return RendererInterface
     */
    public static function renderer()
    {
        return self::get('renderer');
    }

    /**
     * @return ResourceManager
     */
    public static function resourceManager()
    {
       return self::get('resource_manager');
    }

    /**
     * @return string full path to the package directory inside vendors
     */
    public static function packagePath()
    {
        return self::get('package_path');
    }

    public static function get($name)
    {
        if (self::$hub === null) {
            self::$hub = new Hub(self::getDefaultDefinitions());
        }
        return self::$hub->get($name);
    }

    protected static function getDefaultDefinitions()
    {
        return [
            new Item(
                'renderer',
                'template_finder',
                function (RendererInterface &$renderer = null, TemplateFinder $templateFinder) {
                    if (!$renderer) {
                        $renderer = new SimpleRenderer($templateFinder);
                    } else {
                        $renderer->setFinder($templateFinder);
                    }
                }
            ),
            new Value('template_finder', function () {
                return new TemplateFinder([self::packagePath() . '/resources/views']);
            }),
            new Value('package_path', dirname(__DIR__)), // @todo immutable https://github.com/Nayjest/di-hub/issues/4
            new Item('resource_manager', ['css_resources', 'js_resources'], function(ResourceManager &$manager = null, array $css, array $js) {
                    $manager = new ResourceManager($css, $js);
            }),
            new Value('css_resources', [
                'twitter_bootstrap' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'
            ]),
            new Value('js_resources', [
                'twitter_bootstrap' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
                'jquery' => 'https://code.jquery.com/jquery-3.2.1.min.js'
            ])
        ];
    }
}
