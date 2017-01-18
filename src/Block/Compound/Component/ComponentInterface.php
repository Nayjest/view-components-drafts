<?php

namespace ViewComponents\Core\Block\Compound\Component;

use ViewComponents\Core\Block\Compound;

interface ComponentInterface
{
    /**
     * Returns component ID.
     * Must be unique inside compound block.
     *
     * @return string
     */
    public function getId();

    public function handle($eventId, Compound $root);
}