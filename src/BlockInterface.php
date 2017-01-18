<?php

namespace ViewComponents\Core;

interface BlockInterface
{
    public function getSortPosition();

    /**
     * @param int $pos
     * @return $this
     */
    public function setSortPosition($pos);

    public function render();
    public function __toString();

    public function hide();
    public function show();
    public function isVisible();
    public function isHidden();
    public function setVisibility($isVisible);
    public function placeInto(ContainerInterface $parentNode);
}