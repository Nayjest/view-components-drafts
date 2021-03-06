<?php

namespace ViewComponents\Core\Rendering;

use InvalidArgumentException;

/**
 * Renderer for native PHP templates.
 */
class SimpleRenderer implements RendererInterface
{

    /** @var TemplateFinder */
    protected $finder;

    /**
     * SimpleRenderer constructor.
     *
     * @param TemplateFinder|null $finder
     */
    public function __construct(TemplateFinder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Renders template and returns output.
     *
     * @param string $template template name
     * @param array $viewData
     * @return string
     */
    public function render($template, array $viewData = [])
    {
        $filePath = $this->finder->getTemplatePath($template);
        if ($filePath == false) {
            throw new InvalidArgumentException("Can't load template '$template'");
        }
        ob_start();
        extract($viewData);
        include($filePath);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    /**
     * @return TemplateFinder
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * @param TemplateFinder $finder
     */
    public function setFinder($finder)
    {
        $this->finder = $finder;
    }
}
