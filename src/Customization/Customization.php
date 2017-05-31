<?php

namespace ViewComponents\Core\Customization;

use ViewComponents\Core\Block\Block;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\ContainerInterface;
use ViewComponents\Core\Services;

class Customization
{

    //private $path;
    private $config;
    private $options = [];

    public function __construct(array $config, array $options = null)
    {
        // todo
//        Services::renderer()->getFinder()->registerPath(
//            Services::get('core_path') . '/resources/views/twitter_bootstrap',
//            true
//        );
        $this->config = $config;

        if (isset($this->config['options'])) {
            $this->options = $this->config['options'];
            unset($this->config['options']);
        }
        if ($options) {
            $this->options = array_merge($this->options, $options);
        }
    }

    public function apply(BlockInterface $block)
    {
        $this->applyInternal($block, false);
    }

    protected function applyToRoot(BlockInterface $block)
    {

    }

    protected function applyInternal(BlockInterface $block, $isRecursiveCall)
    {
        if ($isRecursiveCall === false) {
            $this->applyToRoot($block);
        }
        $this->customize($block);
        if ($block instanceof ContainerInterface) {
            foreach ($block->getInnerBlocks() as $block) {
                $this->applyInternal($block, true);
            }
        }
        if ($block instanceof Compound) {
            $this->applyInternal($block->containerBlock, true);
        }
    }

    protected function customize(BlockInterface $block)
    {
        foreach ($this->config as $className => $callback) {
            if ($block instanceof $className) {
                call_user_func($callback, $block, $this->options);
            }
        }
    }

}