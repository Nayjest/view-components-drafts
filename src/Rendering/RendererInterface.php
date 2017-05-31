<?php

namespace ViewComponents\Core\Rendering;

interface RendererInterface
{
    /**
     * Renders template and returns output.
     *
     * @param string $template template name
     * @param array $viewData view data
     * @return string
     */
    public function render($template, array $viewData = []);

    /**
     * @return TemplateFinder
     */
    public function getFinder();

    /**
     * @param TemplateFinder $finder
     * @return $this
     */
    public function setFinder(TemplateFinder $finder);
}
