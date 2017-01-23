<?php

namespace ViewComponents\Core\Block\Compound\Component;

interface BlockProviderInterface extends ComponentInterface
{
    public function providesBlock($blockId);
    public function getBlock($id);
}