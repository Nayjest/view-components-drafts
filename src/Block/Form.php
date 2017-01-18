<?php

namespace ViewComponents\Core\Block;

use ViewComponents\Core\Block\Compound\Component\Event;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Form\InputInterface;
use ViewComponents\Core\Compound\CompoundMagicTrait;

class Form extends Compound
{
    use CompoundMagicTrait;

    const SUBMIT_SORT_POSITION = 99;
    const EVENT_UPDATE_VALUES = 'form_update_values';
    const EVENT_UPDATE_ERRORS = 'form_update_errors';

    private $inputData = [];
    private $errors = [];

    public function __construct(array $components = [])
    {
        $this
            ->addComponents([
                new InnerBlock('form', new Tag('form')),
                new InnerBlock(
                    'form.submit_button',
                    Tag::make('input')
                        ->setAttribute('type', 'submit')
                        ->setSortPosition(self::SUBMIT_SORT_POSITION)
                ),
                Event::make(self::EVENT_UPDATE_VALUES, [$this, 'updateInputBlockValues'])
                    ->after(Compound::EVENT_ATTACH_INNER_BLOCKS)
                    ->before(Compound::EVENT_FINALIZE),
                Event::make(self::EVENT_UPDATE_ERRORS, [$this, 'updateInputBlockErrors'])
                    ->after(self::EVENT_UPDATE_VALUES)
                    ->before(Compound::EVENT_FINALIZE),
            ])
            ->addComponents($components);
    }

    /**
     * @return mixed
     */
    public function getInputData()
    {
        return $this->inputData;
    }

    public function getInputValue($key, $default = null)
    {
        return array_key_exists($key, $this->inputData) ? $this->inputData[$key] : $default;
    }

    public function hasInputValue($key)
    {
        return array_key_exists($key, $this->inputData);
    }

    /**
     * @param array $inputData
     * @return $this
     */
    public function setInputData(array $inputData)
    {
        $this->inputData = $inputData;
        return $this;
    }

    /**
     * @param array $inputData
     * @return $this
     */
    public function mergeInputData(array $inputData)
    {
        $this->inputData = array_merge($this->inputData, $inputData);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function addErrors($inputName, array $errors)
    {
        if (!array_key_exists($inputName, $this->errors)) {
            $this->errors[$inputName] = $errors;
            return $this;
        }
        $this->errors[$inputName] = array_unique(array_merge($this->errors[$inputName], $errors));
        return $this;
    }

    /**
     * Finds input by input name
     *
     * @param string $name
     * @return InputInterface|null
     */
    public function findInputNamed($name)
    {
        foreach ($this->getBlocks() as $block) {
            if ($block instanceof InputInterface && $block->getName() === $name) {
                return $block;
            }
        }
        return null;
    }

    /**
     * @internal
     */
    public function updateInputBlockValues()
    {
        foreach ($this->getInputData() as $key => $value) {
            $input = $this->findInputNamed($key);
            if ($input) {
                $input->setValue($value);
            }
        }
    }

    /**
     * @internal
     */
    public function updateInputBlockErrors()
    {
        foreach ($this->getErrors() as $key => $errors) {
            $input = $this->findInputNamed($key);
            if ($input) {
                $input->setErrors($errors);
            }
        }
    }
}
