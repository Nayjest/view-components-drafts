<?php

namespace ViewComponents\Core\Block\Form;

use ViewComponents\Core\Block\Block;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Tag;
use ViewComponents\Core\Compound\Component;

/**
 * @property Tag $inputBlock
 * @method Tag getInputBlock()
 * @method $this setInputBlock(Tag $block)
 */
class Input extends AbstractInput
{
    public function __construct($name, $label = null, $value = null)
    {
        parent::__construct($name, $label, $value);
        $this->addComponent(new InnerBlock(
            'container.input',
            Tag::make('input')->setSortPosition(2),
            function () {
                $this->inputBlock
                    ->setAttribute('name', $this->getName())
                    ->setAttribute('value', $this->getValue());
            }
        ));
    }
}