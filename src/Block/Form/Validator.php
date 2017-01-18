<?php

namespace ViewComponents\Core\Block\Form;

use Exception;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Compound\Component\Event;
use ViewComponents\Core\Block\Form;

class Validator implements ComponentInterface
{
    const EVENT_FORM_VALIDATE = 'form_validate';
    /**
     * @var
     */
    private $inputName;
    /**
     * @var callable
     */
    private $rule;

    private $id;

    public function __construct($inputName, callable $rule)
    {
        $this->id = $inputName . '_validator' . rand(0, PHP_INT_MAX);
        $this->inputName = $inputName;
        $this->rule = $rule;
    }

    public function handle($eventId, Compound $root)
    {
        if ($eventId === Compound::EVENT_SET_ROOT) {
            $root->addComponent(
                Event::make(self::EVENT_FORM_VALIDATE)
                ->after(Form::EVENT_UPDATE_VALUES)
                ->before(Form::EVENT_UPDATE_ERRORS)
            );
        } elseif ($eventId === self::EVENT_FORM_VALIDATE) {
            if (!$root instanceof Form) {
                throw new Exception("Invalid root, form expected");
            }
            \dump('validate input ' . $this->inputName);
           $this->validateForm($root);

        }
    }

    protected function validateForm(Form $form)
    {
        if (empty($form->getInputData())) {
            return;
        }
        $result = $this->validateValue($form->getInputValue($this->inputName));
        if ($result === false) {
            $errors = [
                "Wrong {$this->inputName} value."
            ];
        } elseif ($result === true) {
            $errors = [];
        } elseif (is_array($result)) {
            $errors = $result;
        } else {
            throw new \Exception("Invalid validation rule result for {$this->inputName} input");
        };
        $form->addErrors($this->inputName, $errors);
    }

    public function validateValue($value)
    {
        return call_user_func($this->rule, $value);
    }

    public function getId()
    {
        return $this->id;
    }
}