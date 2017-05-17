<?php

namespace ViewComponents\Core;

use ViewComponents\Core\Block\Tag;

class ResourceManager
{
    protected $css = [];
    protected $js = [];
    protected $requiredJs = [];
    protected $requiredCss = [];

    public function __construct(array $css = [], array $js = [])
    {
        $this->css = $css;
        $this->js = $js;
    }

    public function requireJs($name)
    {
        if (!in_array($name, $this->requiredJs)) {
            $this->requiredJs[] = $name;
        }
        return $this;
    }

    public function requireCss($name)
    {
        if (!in_array($name, $this->requiredCss)) {
            $this->requiredCss[] = $name;
        }
        return $this;
    }

    public function render()
    {
        $out = '';
        foreach ($this->requiredCss as $name) {
            if (isset($this->css[$name])) {
                $out .= $this->renderCss($this->css[$name]);
            }
        }
        foreach ($this->requiredJs as $name) {
            if (isset($this->js[$name])) {
                $out .= $this->renderJs($this->js[$name]);
            }
        }
        return $out;
    }

    protected function renderCss($url, array $attributes = [])
    {
        return Tag::make('link', array_merge([
            'type' => 'text/css',
            'rel' => 'stylesheet',
            'href' => $url,
            'media' => 'all'
        ], $attributes))->render();
    }

    protected function renderJs($url, array $attributes = [])
    {
        $type = 'text/javascript';
        return Tag::make('script', array_merge(['src' => $url, 'type' => $type], $attributes))->render();
    }
}
