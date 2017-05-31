<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Nayjest\DI\Definition\Relation;
use Nayjest\DI\HubInterface;
use Nayjest\DI\SubHub;
use ViewComponents\Core\Block\Compound;

class SubComponent implements ComponentInterface
{
    protected $parentId;
    protected $component;
    protected $id;

    protected function getId()
    {
        if ($this->id === null) {
            $this->id = 'subComponent' . rand(0, PHP_INT_MAX - 1);
        }
        return $this->id;
    }

    public function __construct($parentId, ComponentInterface $component)
    {
        $this->parentId = $parentId;
        $this->component = $component;
    }

    public function register(HubInterface $hub)
    {
        $hub->addDefinition(
            new Relation(
                $this->parentId, null,
                function (Compound $parent) {
                    $parent->addComponent($this->component);
                }
            )
        );
    }
}
