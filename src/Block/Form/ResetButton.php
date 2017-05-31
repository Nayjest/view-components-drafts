<?php

namespace ViewComponents\Core\Block\Form;

use ViewComponents\Core\Block\Block;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\ListBlock;
use ViewComponents\Core\Block\Tag;
use ViewComponents\Core\Services;

class ResetButton extends InnerBlock
{
    const SORT_POSITION = Form::SUBMIT_SORT_POSITION - 1;

    protected function getId()
    {
        return 'resetButton';
    }

    public function __construct($text = 'Reset', array $attributes = [])
    {
        $attributes['onclick'] = 'var form = jQuery(this).parents().filter("form");'
            . 'form.find("input:not([type=\'submit\']), select").val("");'
            . 'form.submit(); return false;';
        $attributes['type'] = 'reset';

        parent::__construct(
            $this->getId(),
            Form::FORM_BLOCK,
            Tag::make('button', $attributes, [
                new Block($text),
                // Will require jQuery on rendering
                new Block(function(){
                    Services::resourceManager()->requireJs('jquery');
                })
            ])->setSortPosition(static::SORT_POSITION)
        );
    }
}