<?php

namespace ViewComponents\Core\Block\Form;

use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\BlockInterface;

interface InputInterface extends BlockInterface, ComponentInterface
{
    public function getName();

    public function setName($name);

    public function getLabel();

    public function setLabel($label);

    public function getValue();

    public function setValue($value);

    public function getErrors();

    public function hasErrors();

    public function setErrors(array $errors);

    public function addErrors(array $errors);
}
