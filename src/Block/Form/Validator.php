<?php

namespace ViewComponents\Core\Block\Form;

use Exception;
use Nayjest\DI\HubInterface;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Form;

class Validator implements ComponentInterface
{
    /**
     * @var
     */
    private $inputName;
    /**
     * @var callable
     */
    private $rule;

    /**
     * Validator constructor.
     * @param $inputName
     * @param callable $rule function that returns
     *                       true or empty array on success
     *                       and returns false or string[] $errorMessages if validation failed
     */
    public function __construct($inputName, callable $rule)
    {
        $this->inputName = $inputName;
        $this->rule = $rule;
    }

    public function register(HubInterface $hub)
    {
        $hub->builder()->defineRelation(Form::ERRORS, null, function (&$errors) use ($hub) {
            /** @var Form $form */
            $form = $hub->get(Form::ROOT_BLOCK);
            $data = $form->requestData;
            if (!array_key_exists($this->inputName, $data)) {
                return;
            }
            $result = $this->validateValue($data[$this->inputName]);
            $errors[$this->inputName] = $this->validationResultToErrors($result);
        });
    }

    public function validateValue($value)
    {
        return call_user_func($this->rule, $value);
    }

    protected function validationResultToErrors($validationResult)
    {
        if ($validationResult === false) {
            return [
                "Wrong {$this->inputName} value."
            ];
        } elseif ($validationResult === true) {
            return [];
        } elseif (is_array($validationResult)) {
            return $validationResult;
        } else {
            throw new Exception("Invalid validation rule result for {$this->inputName} input");
        }
    }
}