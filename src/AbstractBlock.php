<?php

namespace ViewComponents\Core;


abstract class AbstractBlock implements BlockInterface
{
    use MakeTrait;

    private $sortPosition;

    private $visibility = true;

    /**
     * @return string
     */
    abstract protected function renderInternal();


    public final function render()
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->renderInternal();
    }


    public function hide()
    {
        $this->visibility = false;
        return $this;
    }

    public function show()
    {
        $this->visibility = true;
        return $this;
    }

    public function isVisible()
    {
        return $this->visibility;
    }

    public function isHidden()
    {
        return !$this->visibility;
    }

    /**
     * @param $isVisible
     * @return $this
     */
    public function setVisibility($isVisible)
    {
        $this->visibility = (boolean)$isVisible;
        return $this;
    }

    /**
     * @param int|float|null $sortPosition
     * @return $this
     */
    public function setSortPosition($sortPosition)
    {
        $this->sortPosition = $sortPosition;
        return $this;
    }

    /**
     * @return float|int|null
     */
    public function getSortPosition()
    {
        return $this->sortPosition;
    }

    public function placeInto(ContainerInterface $parentNode)
    {
        $parentNode->hasInnerBlock($this) || $parentNode->addInnerBlock($this);
        return $this;
    }
    
    public function __toString()
    {
        return $this->render();
    }
}