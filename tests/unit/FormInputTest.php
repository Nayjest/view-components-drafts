<?php

namespace ViewComponents\Core\Test;

use PHPUnit\Framework\TestCase;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\Block\Block;
use ViewComponents\Core\Block\Input;

class FormInputTest extends TestCase
{
    /**
     * @var FormInput
     */
    private $input;

    public function setUp()
    {
        $this->input = new Input('input_name', 'label_value', 'input_value');
    }

    public function testGetLabelTextBlock()
    {
        self::assertInstanceOf(BlockInterface::class, $this->input->getlabelTextBlock());
        self::assertInstanceOf(BlockInterface::class, $this->input->labelTextBlock);
    }

    public function testLabel()
    {
        self::assertEquals('label_value', $this->input->label);
        self::assertEquals('label_value', $this->input->getLabel());
        self::assertEquals('label_value', $this->input->labelTextBlock->getContent());

        // test label visibility
        $this->input->label = '';
        self::assertFalse($this->input->labelBlock->isVisible());
        $this->input->label = '1';
        self::assertTrue($this->input->labelBlock->isVisible());

        $this->input->label = 'label_value2';
        self::assertEquals('label_value2', $this->input->label);
        self::assertEquals('label_value2', $this->input->getLabel());
        self::assertEquals('label_value2', $this->input->labelTextBlock->getContent());
        self::assertEquals('label_value2', $this->input->getLabelTextBlock()->getContent());
        $this->input->setLabel('label_value3');
        self::assertEquals('label_value3', $this->input->label);
        self::assertEquals('label_value3', $this->input->getLabel());
        self::assertEquals('label_value3', $this->input->labelTextBlock->getContent());
        self::assertEquals('label_value3', $this->input->getLabelTextBlock()->getContent());
        $this->input->setLabelTextBlock((new Block())->beforeRender(function(Block $block){
            $block->setData(
                str_replace('!', '', $block->getData()) . '!'
            );
        }));
        $this->input->setLabel('label_value4');
        self::assertEquals('label_value4!', $this->input->labelTextBlock->render());
        self::assertEquals('label_value4!', $this->input->getLabelTextBlock()->render());
    }

    public function testValue()
    {
        $val = 'input_value';
        self::assertEquals($val, $this->input->value);
        self::assertEquals($val, $this->input->getValue());
        self::assertEquals($val, $this->input->inputBlock->getAttribute('value'));
        $val = $this->input->value = 'value2';
        self::assertEquals($val, $this->input->value);
        self::assertEquals($val, $this->input->getValue());
        self::assertEquals($val, $this->input->inputBlock->getAttribute('value'));
    }
}