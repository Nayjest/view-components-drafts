<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Nayjest\DI\Definition\DefinitionInterface;
use Nayjest\DI\Definition\Value;
use Nayjest\DI\Hub;
use Nayjest\DI\HubInterface;
use Nayjest\DI\SubHub;
use ViewComponents\Core\Common\MagicHubAccessTrait;

trait ComponentTrait
{
    use MagicHubAccessTrait;

    public function externalId($internalId)
    {
        return $this->getId() . ucfirst($internalId);
    }

    /**
     * @param array|DefinitionInterface[] $data
     */
    protected function initialize(array $data)
    {
        $this->makeHub();
        $data['component'] = $this;
        foreach ($data as $id => $value) {
            if ($value instanceof DefinitionInterface) {
                $this->hub->addDefinition($value);
            } else {
                if ($this->hub->has($id)) {
                    $this->hub->set($id, $value);
                } else {
                    $this->hub->addDefinition(new Value($id, $value));
                }
            }
        }
    }

    private function makeHub()
    {
        if (!$this->hub instanceof SubHub) {
            $this->hub = new SubHub([$this, 'externalId'], $this->hub ?: new Hub());
        }
    }

    public function register(HubInterface $hub)
    {
        $this->hub->register($hub);
    }
}
