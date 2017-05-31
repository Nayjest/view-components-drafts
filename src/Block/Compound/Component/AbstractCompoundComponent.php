<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Nayjest\DI\Definition\DefinitionInterface;
use ViewComponents\Core\Block\Compound;

abstract class AbstractCompoundComponent extends Compound implements ComponentInterface
{
    use ComponentTrait;

    abstract protected function getId();

    /**
     * AbstractCompoundComponent constructor.
     *
     * @param array|DefinitionInterface[]|ComponentInterface[] $data
     */
    public function __construct(array $data = [])
    {
        $components = [];
        foreach($data as $key => $value) {
            if ($value instanceof ComponentInterface) {
                $components[] = $value;
                unset($data[$key]);
            }
        }
        $this->makeHub();
        parent::__construct($components);
        $this->initialize($data);
    }
}
