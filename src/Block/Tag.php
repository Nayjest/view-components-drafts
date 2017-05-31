<?php

namespace ViewComponents\Core\Block;

use ViewComponents\Core\AbstractContainer;
use ViewComponents\Core\DataPresenterInterface;
use ViewComponents\Core\DataPresenterTrait;

class Tag extends AbstractContainer implements DataPresenterInterface
{
    use DataPresenterTrait;

    private static $noClosingTag = [
        'link',
        'track',
        'param',
        'area',
        'command',
        'col',
        'base',
        'meta',
        'hr',
        'source',
        'img',
        'keygen',
        'br',
        'wbr',
        'colgroup', # when the span is present
        'input',
        'frame',
        'basefont',
        'isindex'
    ];


    /**
     * HTML tag attributes.
     * Keys are attribute names and values are attribute values.
     * @var array
     */
    protected $attributes = [];

    public static function make($name = 'div', array $attributes = null, array $innerBlocks = null)
    {
        return new Tag($name, $attributes, $innerBlocks);
    }

    public function __construct($name = 'div', array $attributes = null, array $innerBlocks = null)
    {
        $this->setName($name);
        if ($attributes) {
            $this->setAttributes($attributes);
        }
        if ($innerBlocks) {
            $this->addInnerBlocks($innerBlocks);
        }
    }

    /**
     * Renders HTML tag attributes.
     *
     * @param array $attributes
     * @return string
     */
    public static function renderAttributes(array $attributes)
    {
        $html = [];
        foreach ($attributes as $key => $value) {
            $escaped = htmlentities($value, ENT_QUOTES, 'UTF-8', false);
            $html[] = is_numeric($key) ?
                ($escaped . '="' . $escaped . '""')
                :
                ("$key=\"$escaped\"");
        }
        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Returns html tag attributes.
     * Keys are attribute names and values are attribute values.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Returns HTML tag attribute by name.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        } else {
            return $default;
        }
    }

    /**
     * Sets HTML tag attribute.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @param string $cssClass
     * @return $this
     */
    public function addClass($cssClass) {
        $classAttribute = $this->getAttribute('class', '');
        $classes = explode(' ', $classAttribute);
        if (!in_array($cssClass, $classes)) {
            $classes[] = $cssClass;
            $this->setAttribute('class', join(' ', $classes));
        }
        return $this;
    }

    /**
     * @param string[] $cssClasses
     * @return $this
     */
    public function addClasses(array $cssClasses) {
        foreach($cssClasses as $cssClass) {
            $this->addClass($cssClass);
        }
        return $this;
    }

    public function removeClass($cssClass) {
        $classAttribute = $this->getAttribute('class');
        if (!$classAttribute) {
            return $this;
        }
        $classes = explode(' ', $classAttribute);
        if(($key = array_search($cssClass, $classes)) !== false) {
            unset($classes[$key]);
            $this->setAttribute('class', join(' ', $classes));
        }
        return $this;
    }

    public function removeClasses(array $cssClasses)
    {
        foreach($cssClasses as $cssClass) {
            $this->removeClass($cssClass);
        }
        return $this;
    }

    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * Sets html tag attributes.
     * Keys are attribute names and values are attribute values.
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Adds attributes to HTML tag.
     * New attributes overwrites existing with same names.
     *
     * @param array $attributes
     * @return $this
     */
    public function addAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Renders opening tag.
     *
     * @return string
     */
    protected function renderOpening()
    {
        return '<'
        . $this->getName()
        . static::renderAttributes($this->getAttributes())
        . ($this->hasNoClosingTag() ? '/' : '')
        . '>';
    }

    /**
     * Renders closing tag.
     *
     * @return string
     */
    protected function renderClosing()
    {
        return $this->hasNoClosingTag() ? '' : "</{$this->getName()}>";
    }

    protected function hasNoClosingTag()
    {
        return in_array($this->getName(), static::$noClosingTag);
    }

    private $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets tag name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    protected function renderInternal()
    {
        if ($this->isHidden()) {
            return '';
        }

        return $this->renderOpening()
        . ($this->getData() !== null ? (string)$this->getData() : '')
        . $this->renderInnerBlocks()
        . $this->renderClosing();
    }
}
