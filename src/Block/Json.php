<?php

namespace ViewComponents\Core\Block;

use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\BlockTrait;
use ViewComponents\Core\DataPresenterInterface;
use ViewComponents\Core\DataPresenterTrait;

class Json implements BlockInterface, DataPresenterInterface
{
    use BlockTrait;
    use DataPresenterTrait;

    private $options;

    public function __construct($data = null, $options = 0)
    {
        $this->setData($data);
        $this->options = $options;
    }

    /**
     * @param int $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    protected function renderInternal()
    {
        return json_encode($this->data, $this->options);
    }
}
