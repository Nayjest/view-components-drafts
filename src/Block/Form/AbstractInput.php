<?php

namespace ViewComponents\Core\Block\Form;

use ViewComponents\Core\Block\CollectionPresenter;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\BlockComponentInterface;
use ViewComponents\Core\Block\Compound\Component\ComponentFeaturesTrait;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Tag;
use ViewComponents\Core\Compound\CompoundMagicTrait;

/**
 * Class AbstractInput
 *
 * @property Tag $labelBlock
 * @method Tag getLabelBlock()
 * @method $this setLabelBlock(Tag $block)
 *
 * @property Tag $containerBlock
 * @method Tag getContainerBlock()
 * @method $this setContainerBlock(Tag $block)
 *
 * @property Tag $errorsBlock
 * @method Tag getErrorsBlock()
 * @method $this setErrorsBlock(Tag $block)
 *
 * @property CollectionPresenter $errorCollectionBlock
 * @method CollectionPresenter getErrorCollectionBlock()
 * @method $this setErrorCollectionBlock(CollectionPresenter $block)
 *
 * @property Tag $errorBlock
 * @method Tag getErrorBlock()
 * @method $this setErrorBlock(Tag $block)
 */
class AbstractInput extends Compound implements InputInterface, BlockComponentInterface
{
    use ComponentFeaturesTrait;
    use CompoundMagicTrait;

    public $name;
    public $label;
    public $value;
    public $errors = [];

    public function __construct($name, $label = null, $value = null)
    {
        $this->addComponents([
            new InnerBlock('container', new Tag('div')),

            new InnerBlock(
                'container.label',
                Tag::make('label')->setSortPosition(1),
                function () {
                    $this->labelBlock
                        ->setVisibility($this->label !== null)
                        ->setData($this->label);
                }
            ),
            new InnerBlock(
                'container.errors',
                Tag::make('div')->setSortPosition(3),
                function () {
                    $this->errorsBlock->setVisibility($this->hasErrors());
                }
            ),
            new InnerBlock(
                'container.errors.error_collection',
                CollectionPresenter::make(),
                function () {
                    $this->errorCollectionBlock
                        ->setData($this->errors)
                        ->setRecordView($this->errorBlock);
                }
            ),
            new InnerBlock('error_collection.error', new Tag('p')),
        ]);
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
    }

    protected function getDestination()
    {
        $name = str_replace('.', '_', $this->name);
        return [Form::class, "form.{$name}_input"];
    }

    /**
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @param string[] $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @param string[] $errors
     * @return $this
     */
    public function addErrors(array $errors)
    {
        $this->errors = array_unique(array_merge($this->getErrors(), $errors));
        return $this;
    }

    /**
     * @param mixed $name
     * @return Input
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $label
     * @return Input
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param mixed $value
     * @return Input
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}