<?php

namespace ViewComponents\Core\Block;

use Nayjest\DI\Hub;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\BlockTrait;
use ViewComponents\Core\Common\MagicHubAccessTrait;
use ViewComponents\Core\ContainerInterface;
use ViewComponents\Core\Common\MakeTrait;

/**
 * Class Compound
 *
 * Compound Block constructed from components.
 *
 * @property-read ContainerInterface $containerBlock
 */
class Compound implements BlockInterface
{
    use MakeTrait;
    use BlockTrait;
    use MagicHubAccessTrait;

    /** @var  Hub */
    protected $hub;

    const CONTAINER_BLOCK = 'containerBlock';

    /**
     * Compound constructor.
     * @param ComponentInterface[] $components
     */
    public function __construct(array $components = [])
    {
        $this->hub = new Hub();
        $this->hub->builder()->define('root', $this);
        $this->addComponents($components);
        if (!$this->hub->has(self::CONTAINER_BLOCK)) {
            $this->hub->builder()->define(self::CONTAINER_BLOCK, new Container());
        }
    }

    public function addComponent(ComponentInterface $component)
    {
        $component->register($this->hub);
        return $this;
    }

    /**
     * @param ComponentInterface[] $components
     * @return $this
     */
    public function addComponents(array $components)
    {
        foreach($components as $component) {
            $this->addComponent($component);
        }
        return $this;
    }

    protected function renderInternal()
    {
        return $this->containerBlock->render();
    }
}
