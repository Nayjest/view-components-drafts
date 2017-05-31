<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Nayjest\DI\Definition\DefinitionInterface;
use Nayjest\DI\HubInterface;

abstract class AbstractComponent implements ComponentInterface
{
    use ComponentTrait;

    /**
     * @var HubInterface
     */
    protected $hub;

    abstract protected function getId();

    /**
     * AbstractComponent constructor.
     *
     * @param array|DefinitionInterface[] $data
     */
    public function __construct(array $data = [])
    {
        $this->initialize($data);
    }
}
