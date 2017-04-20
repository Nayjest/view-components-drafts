<?php

namespace ViewComponents\Core\Block\Form;

use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Tag;

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
            Tag::make('input')->setSortPosition(2)
        ));
        $this->hub->builder()
            ->defineRelation('inputBlock', 'name', function (Tag $input, $name) {
                $input->setAttribute('name', $name);
            })->defineRelation('inputBlock', 'value', function (Tag $input, $value) {
                $input->setAttribute('value', $value);
            });
    }
}