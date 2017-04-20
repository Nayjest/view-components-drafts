<?php

namespace ViewComponents\Core\Block;

use ViewComponents\Core\Block\Compound\Component\InnerBlock;

/**
 * Class Form
 *
 * @property string[][] $errors
 * @method $this setErrors(array $errors)
 * @method string[][] getErrors(array $errors)
 *
 * @property array $inputData
 * @method $this setInputData(array $data)
 * @method array getInputData()
 *
 * @property array $requestData
 * @method $this setRequestData(array $data)
 * @method array getRequestData()
 */
class Form extends Compound
{
    const SUBMIT_SORT_POSITION = 99;

    public function __construct(array $components = [])
    {
        parent::__construct($components);
        $this->hub->builder()
            ->define('inputData', [], true)
            ->define('requestData', [])
            ->usedBy('inputData', function(array &$inputData, array $newRequestData, array $oldRequestData = null) {
                if ($oldRequestData) {
                    foreach(array_keys($oldRequestData) as $key) {
                        if (!array_key_exists($key, $newRequestData)) {
                            unset($inputData[$key]);
                        }
                    }
                }
                $inputData = array_merge($inputData, $newRequestData);
            })
            ->define('errors', []);

        if (!$this->hub->has('formBlock')) {
            $this->addComponent(new InnerBlock('form', new Tag('form')));
        }
        if (!$this->hub->has('submitButtonBlock')) {
            $this->addComponent(new InnerBlock(
                'form.submitButton',
                Tag::make('input')
                    ->setAttribute('type', 'submit')
                    ->setSortPosition(self::SUBMIT_SORT_POSITION)
            ));
        }
    }

    public function getInputValue($key, $default = null)
    {
        $data = $this->getInputData();
        return array_key_exists($key, $data) ? $data[$key] : $default;
    }

    public function hasInputValue($key)
    {
        return array_key_exists($key, $this->getInputData());
    }
/*

    public function mergeInputData(array $inputData)
    {
        $existingData = $this->getInputData();
        $this->setInputData(array_merge($existingData, $inputData));
        return $this;
    }
*/
    public function addErrors($inputName, array $errors)
    {
        if (!array_key_exists($inputName, $this->errors)) {
            $this->errors[$inputName] = $errors;
            return $this;
        }
        $this->errors[$inputName] = array_unique(array_merge($this->errors[$inputName], $errors));
        return $this;
    }
}
