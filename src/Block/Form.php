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
 *
 * @property-read Tag $formBlock
 * @property-read Tag $submitButtonBlock
 *
 */
class Form extends Compound
{
    const SUBMIT_SORT_POSITION = 99;

    const INPUT_DATA = 'inputData';
    const REQUEST_DATA = 'requestData';
    const ERRORS = 'errors';

    const FORM_BLOCK = 'formBlock';
    const SUBMIT_BUTTON_BLOCK = 'submitButtonBlock';

    public function __construct(array $components = [])
    {
        parent::__construct($components);
        $this->hub->builder()
            ->define(self::INPUT_DATA, []) // @todo readonly
            ->define(self::REQUEST_DATA, [])
            ->usedBy(self::INPUT_DATA, function(array &$inputData, array $newRequestData, array $oldRequestData = null) {
                if ($oldRequestData) {
                    foreach(array_keys($oldRequestData) as $key) {
                        if (!array_key_exists($key, $newRequestData)) {
                            unset($inputData[$key]);
                        }
                    }
                }
                $inputData = array_merge($inputData, $newRequestData);
            })
            ->define(self::ERRORS, []);

        if (!$this->hub->has(self::FORM_BLOCK)) {
            $this->addComponent(
                InnerBlock::make(self::FORM_BLOCK, Tag::make('form')->setBlockSeparator(' '))
            );
        }
        if (!$this->hub->has(self::SUBMIT_BUTTON_BLOCK)) {
            $this->addComponent(new InnerBlock(
                self::SUBMIT_BUTTON_BLOCK,
                self::FORM_BLOCK,
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
