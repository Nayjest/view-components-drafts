<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Nayjest\DI\HubInterface;

interface ComponentInterface
{
    public function register(HubInterface $hub);
}
