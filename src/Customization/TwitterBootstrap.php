<?php

namespace ViewComponents\Core\Customization;

use ViewComponents\Core\Block\Block;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\ContainerInterface;
use ViewComponents\Core\Services;

class TwitterBootstrap extends Customization
{
    const CONTROL_SIZE_SMALL = 'sm';
    const CONTROL_SIZE_LARGE = 'lg';
    const CONTROL_SIZE_DEFAULT = null;

    public static function make(array $options = null)
    {
        return new self($options);
    }

    public function __construct(array $options = null)
    {
        parent::__construct(
            include Services::packagePath() . '/resources/customizations/twitter_bootstrap.php',
            $options
        );
    }

    protected function applyToRoot(BlockInterface $block)
    {
        $triggerBlock = Block::make(function(){
            $this->requireResources();
        })->setSortPosition(-PHP_INT_MAX);
        if ($block instanceof ContainerInterface) {
            $block->addInnerBlock($triggerBlock);
        } elseif ($block instanceof Compound) {
            $block->addComponent(new InnerBlock('resource_trigger', null, $triggerBlock));
        }
    }

    public function requireResources()
    {
        Services::resourceManager()->requireCss('twitter_bootstrap');
    }
}