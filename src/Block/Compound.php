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

    const CONTAINER_BLOCK = 'containerBlock';
    const ROOT_BLOCK = 'rootBlock';

    /** @var  Hub */
    protected $hub;

    /**
     * Compound constructor.
     * @param ComponentInterface[] $components
     */
    public function __construct(array $components = [])
    {
        if ($this->hub === null) { # Another hub class may be used in child classes, see AbstractCompoundComponent.
            $this->hub = new Hub();
        }
        $this->hub->builder()->define(self::ROOT_BLOCK, $this);
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
        foreach ($components as $component) {
            $this->addComponent($component);
        }
        return $this;
    }

    protected function renderInternal()
    {
        return $this->containerBlock->render();
    }

    protected function hasBlock($id)
    {
        return $this->hub->has($id . 'Block');
    }
}
