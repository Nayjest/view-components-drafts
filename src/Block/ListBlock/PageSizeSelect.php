<?php

namespace ViewComponents\Core\Block\ListBlock;

use InvalidArgumentException;
use Nayjest\DI\HubInterface;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Form\AbstractInput;
use ViewComponents\Core\Block\Form\Select;
use ViewComponents\Core\Block\ListBlock;

class PageSizeSelect implements ComponentInterface
{

    private $block;
    private $defaultOptions = [
        50 => 50,
        100 => 100,
        300 => 300,
        1000 => 1000
    ];
    private $defaultValue;

    /**
     * PageSizeSelect constructor.
     * @param AbstractInput|array $inputOrOptions
     */
    public function __construct($inputOrOptions = null, $defaultValue = null)
    {
        $this->defaultValue = $defaultValue;
        if ($inputOrOptions instanceof AbstractInput) {
            $this->block = $inputOrOptions;
            if ($defaultValue!==null) {
                $inputOrOptions->value = $defaultValue;
            }
        } elseif (is_array($inputOrOptions)) {
            if (isset($inputOrOptions[0])) {
                $inputOrOptions = array_combine($inputOrOptions, $inputOrOptions);
            }
            $this->defaultOptions = $inputOrOptions;
        } elseif ($inputOrOptions !== null) {
            throw new InvalidArgumentException;
        }
    }

    /**
     * @return AbstractInput
     */
    public function getBlock()
    {
        if ($this->block === null) {
            $this->block = new Select('page_size', 'Records per page', $this->defaultOptions);
            if ($this->defaultValue!==null) {
                $this->block->value = $this->defaultValue;
            }
        }
        return $this->block;
    }

    public function register(HubInterface $hub)
    {
        $hub->builder()
            ->define('pageSizeSelect.block', [$this, 'getBlock'])
            ->usedBy(InnerBlock::getFullId('form'), function (Form $form, AbstractInput $block) {
                $form->addComponent($block);
            })
            ->defineRelation(
                'pagination.pageSize',
                [InnerBlock::getFullId('form'), 'pageSizeSelect.block'],
                function (&$pageSize, Form $form, AbstractInput $block) {
                    if ($block->hasErrors()) {
                        return;
                    }
                    $value = $form->getInputValue($block->getName());
                    if ($value === '' || $value === null) {
                        return;
                    }
                    $pageSize = $value;
                }
            );
    }
}
