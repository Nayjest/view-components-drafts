<?php

namespace ViewComponents\Core\Block;

use Exception;
use ViewComponents\Core\AbstractBlock;
use ViewComponents\Core\Block\Compound\Component\BlockComponentInterface;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\ContainerInterface;
use ViewComponents\Core\Event\EventDefinitionBuilder;
use ViewComponents\Core\Event\EventSequence;

class Compound extends AbstractBlock
{
    const EVENT_INIT = 'init';
    const EVENT_SET_ROOT = 'set_root';
    const EVENT_UNSET_ROOT = 'unset_root';
    const EVENT_ATTACH_INNER_BLOCKS = 'attach_inner_blocks';
    const EVENT_FINALIZE = 'finalize';

    /** @var ComponentInterface[] */
    private $components = [];

    private $rootContainer;

    /** @var  EventSequence */
    protected $eventSequence;


    /**
     * @param string $eventId
     * @return EventDefinitionBuilder
     */
    public function defineEvent($eventId)
    {
        return $this->getEventSequence()->define($eventId);
    }

    /**
     * @return EventSequence
     */
    protected final function getEventSequence()
    {
        if ($this->eventSequence === null) {
            $this->eventSequence = $sequence = new EventSequence();
            $sequence->define(self::EVENT_INIT)->before(self::EVENT_FINALIZE);
            $sequence->define(self::EVENT_ATTACH_INNER_BLOCKS)->after(self::EVENT_INIT)->before(self::EVENT_FINALIZE);;
            $sequence->define(self::EVENT_FINALIZE)->after(self::EVENT_ATTACH_INNER_BLOCKS);
        }
        return $this->eventSequence;
    }


    public final function shareEventSequenceTo(Compound $compound)
    {

        $compound->eventSequence = $compound->eventSequence
            ? $this->eventSequence->merge($compound->eventSequence)
            : $this->eventSequence;
    }

    /**
     * @return ContainerInterface
     */
    public function getRootContainer()
    {
        if ($this->rootContainer === null) {
            $this->rootContainer = new Container();
        }
        return $this->rootContainer;
    }

    /**
     * @param \ViewComponents\Core\Block\Compound\Component\ComponentInterface[] $components
     * @return $this
     */
    public function addComponents(array $components)
    {
        foreach ($components as $component) {
            $this->addComponent($component);
        }
        return $this;
    }

    /**
     * @param ComponentInterface $component
     * @return $this
     */
    public function addComponent(ComponentInterface $component)
    {
        $id = $component->getId();
        if ($this->hasComponent($id) && $this->getComponent($id) !== $component) {
            $this->removeComponent($id);
        }
        $component->handle(self::EVENT_SET_ROOT, $this);
        $this->components[$id] = $component;
        return $this;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasComponent($id)
    {
        return array_key_exists($id, $this->components);
    }

    public function getComponent($id)
    {
        if (!$this->hasComponent($id)) {
            $className = get_class($this);
            throw new Exception("Trying to get absent component '$id' in $className");
        }
        return $this->components[$id];
    }

    /**
     * @param string $id
     * @return $this
     */
    public function removeComponent($id)
    {
        if ($this->hasComponent($id)) {
            $this->components[$id]->handle(self::EVENT_UNSET_ROOT, $this);
            unset($this->components[$id]);
        }
        return $this;
    }

//    /**
//     * @param $id
//     * @return \ViewComponents\Core\Block\Compound\Component\BlockComponentInterface
//     * @throws Exception
//     */
//    public function getBlockComponent($id)
//    {
//        $component = $this->getComponent($id);
//        if (!($component instanceof BlockComponentInterface)) {
//            throw new Exception("Component '$id' is not BlockComponent.");
//        }
//        return $component;
//    }

    /**
     * @param string $id
     * @return BlockInterface
     * @throws Exception
     */
    public function getBlock($id = null)
    {
        // Required to avoid conflicts if Compound should implement BlockComponentInterface
        if ($id === null && func_num_args() === 0) {
            return $this;
        }
        $component = $this->getComponent($id);
        if (!$component instanceof BlockComponentInterface) {
            throw new Exception(
                "Trying to get absent block '$id', component '$id' is not BlockComponent"
            );
        }
        return $component->getBlock();
    }

    /**
     * @param $id
     * @param BlockInterface $block
     * @return $this|void
     * @throws Exception
     */
    public function setBlock($id, BlockInterface $block)
    {
        $component = $this->getComponent($id);
        if (method_exists($component, 'setBlock')) {
            $component->setBlock($block);
            return $this;
        }
        throw new Exception("Can not set block '$id'");
    }

    public function hasBlock($id)
    {
        return $this->hasComponent($id) && $this->getComponent($id) instanceof BlockComponentInterface;
    }

    /**
     * @return \ViewComponents\Core\Block\Compound\Component\ComponentInterface[]
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @return BlockInterface[]
     */
    public function getBlocks()
    {
        $res = [];
        foreach ($this->getComponents() as $component) {
            if ($component instanceof BlockComponentInterface) {
                $res[$component->getId()] = $component->getBlock();
            }
        }
        return $res;
    }

    // ==============================================================================

    protected function dispatchEvents()
    {
        $sequence = $this->getEventSequence();
        foreach ($sequence as $eventId) {
            $this->dispatchEvent($eventId);
        }
    }

    public function dispatchEvent($eventId)
    {
        foreach ($this->getComponents() as $component) {
            $component->handle($eventId, $this);
        }
    }

    protected function renderInternal()
    {
        $this->dispatchEvents();
        $out = $this->getRootContainer()->render();
        return $out;
    }
}
