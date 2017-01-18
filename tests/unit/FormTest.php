<?php

namespace ViewComponents\Core\Test;

use PHPUnit\Framework\TestCase;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\Block\Block;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Input;

class FormTest extends TestCase
{

    private $form;

    public function setUp()
    {
        $this->form = new Form();
    }


    public function test()
    {
        $from = new Form([
            new Input('test_field1'),
            new Input('test_field2')
        ]);
        $out = $from->render();
        $this->assertContains('<form', $out);
        $this->assertContains('</form>', $out);
        $this->assertContains('input', $out);
        $this->assertContains('test_field1', $out);
        $this->assertContains('test_field2', $out);
    }
}