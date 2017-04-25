<?php

namespace ViewComponents\Core\Block;

use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\BlockTrait;
use ViewComponents\Core\DataPresenterInterface;
use ViewComponents\Core\DataPresenterTrait;

class DataPresenter implements BlockInterface, DataPresenterInterface
{
    use BlockTrait;
    use DataPresenterTrait;

    /**
     * @var BlockInterface
     */
    private $viewBlock;
    /**
     * @var callable
     */
    private $injector;

    public function __construct(callable $dataInjector, BlockInterface $viewBlock = null, $data = null)
    {
        if ($data !== null) {
            $this->setData($data);
        }
        $this->viewBlock = $viewBlock;
        $this->injector = $dataInjector;
    }

    /**
     * @return BlockInterface|null
     */
    public function getViewBlock()
    {
        return $this->viewBlock;
    }

    /**
     * @param BlockInterface|null $viewBlock
     * @return $this
     */
    public function setViewBlock(BlockInterface $viewBlock = null)
    {
        $this->viewBlock = $viewBlock;
        return $this;
    }

    /**
     * @return callable
     */
    public function getInjector()
    {
        return $this->injector;
    }

    /**
     * @param callable $injector
     */
    public function setInjector($injector)
    {
        $this->injector = $injector;
    }

    protected function renderInternal()
    {
        if ($this->viewBlock === null) {
            return call_user_func($this->injector, $this->getData());
        }
        call_user_func($this->injector, $this->getData(), $this->viewBlock);
        return $this->viewBlock->render();
    }
}