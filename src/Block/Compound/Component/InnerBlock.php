<?php

namespace ViewComponents\Core\Block\Compound\Component;

use Nayjest\DI\HubInterface;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\InnerBlock\InnerBlockRelation;
use ViewComponents\Core\BlockInterface;

/**
 * Class InnerBlock
 *
 * 1) Adds target block into compound block hierarchy as inner block
 * 2) Registers <id>Block in hub
 */
class InnerBlock extends AbstractComponent
{

    protected $id;
    protected $parentId;
    protected $relation;

    protected function getId()
    {
        return $this->id;
    }

    /** @var  HubInterface|null */
    protected $externalHub;

    public static function make($path, BlockInterface $block)
    {
        $parts = explode('.', $path);
        list($parentId, $id) = count($parts) > 1 ? $parts : [null, $parts[0]];
        return new static($id, $parentId, $block);
    }

    public function __construct($id, $parentId, BlockInterface $block = null)
    {
        parent::__construct(compact('block'));
        $this->id = str_replace('Block', '', $id);
        $this->setParentId($parentId);
    }

    public function setParentId($id)
    {
        if ($id === null) {
            $id = Compound::CONTAINER_BLOCK;
        }
        if ($id === $this->parentId) {
            return $this;
        }
        $this->parentId = $id;
        if ($this->externalHub) {
            $this->updateRelation();
        }
        return $this;
    }

    protected function updateRelation()
    {
        if ($this->relation !== null) {
            $block = $this->getBlock();
            // old relation will remove block from old container
            $this->hub->set('block', null);
            $this->externalHub->remove($this->relation);
            $this->hub->set('block', $block);
        }
        $this->relation = new InnerBlockRelation($this->parentId, $this->id);
        $this->externalHub->addDefinition($this->relation);
    }

    public function register(HubInterface $hub)
    {
        parent::register($hub);
        $this->externalHub = $hub;
        $this->updateRelation();
    }

    /**
     * @param BlockInterface $block
     */
    public function setBlock(BlockInterface $block)
    {
        $this->hub->set('block', $block);
    }

    /**
     * @return BlockInterface
     */
    public function getBlock()
    {
        return $this->hub->get('block');
    }
}
