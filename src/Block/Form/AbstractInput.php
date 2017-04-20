<?php

namespace ViewComponents\Core\Block\Form;

use Nayjest\DI\HubInterface;
use Nayjest\DI\SubHub;
use ViewComponents\Core\Block\CollectionPresenter;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\DefinitionBuilder;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Tag;

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
 *
 * @property string[] $errors
 * @method string[] getErrors()
 * @method $this setErrors(array $errors)
 *
 * @property string $name
 * @method string getName()
 * @method $this setName(string $name)
 *
 * @property string $label
 * @method string getLabel()
 * @method $this setLabel(string $text)
 *
 * @property mixed $value
 * @method string getValue()
 * @method $this setValue(mixed $value)
 *
 * @property string $parentId
 *
 */
class AbstractInput extends Compound implements ComponentInterface
{
//    public $name;
//    public $label;
//    public $value;
//    public $errors = [];

    public function __construct($name, $label = null, $value = null)
    {
        parent::__construct([
            // replace empty container to 'div' tag
            DefinitionBuilder::make()
                ->define(Compound::CONTAINER_BLOCK, new Tag('div'))
                ->define('parentId', 'formBlock')
            ,
            new InnerBlock(
                'container.label',
                Tag::make('label')->setSortPosition(1)
//                function () {
//                    $this->labelBlock
//                        ->setVisibility($this->label !== null)
//                        ->setData($this->label);
//                }
            ),
            new InnerBlock(
                'container.errorsContainer',
                Tag::make('div')->setSortPosition(3)
//                function () {
//                    $this->errorsBlock->setVisibility($this->hasErrors());
//                }
            ),
            new InnerBlock(
                'errorsContainer.errorCollection',
                CollectionPresenter::make()
//                function () {
//                    $this->errorCollectionBlock
//                        ->setData($this->errors)
//                        ->setRecordView($this->errorBlock);
//                }
            ),
            new InnerBlock('errorCollection.error', new Tag('p'))
        ]);
        $this->hub->builder()
            ->define('name', $name)
            ->define('label', $label)
            ->usedBy('labelBlock', function (Tag $block, $label) {
                $block
                    ->setVisibility($label !== null)
                    ->setData($label);
            })
            ->define('value', $value)
            ->define('errors', [])
            ->usedBy('errorsContainerBlock', function (Tag $block, $errors) {
                $block->setVisibility(!empty($errors));
            })
            ->usedBy('errorCollectionBlock', function (CollectionPresenter $block, $errors) {
                $block->setData($errors);
            })
            ->defineRelation('errorCollectionBlock', 'errorBlock', function (CollectionPresenter $block, $errorBlock) {
                $block->setRecordView($errorBlock);
            });
        ;
    }

    public function hasErrors()
    {
        return !empty($this->hub->get('errors'));
    }


    /**
     * @param string[] $errors
     * @return $this
     */
    public function addErrors(array $errors)
    {
        $this->setErrors(array_unique(array_merge($this->getErrors(), $errors)));
        return $this;
    }

    public function register(HubInterface $hub)
    {
        $prefix = $this->name . 'Input';
        $this->hub = new SubHub($prefix, $this->hub, $hub);
        $hub->builder()
            # Attach to Form.formBlock as inner block
            ->defineRelation($this->parentId, $prefix . 'root', InnerBlock::attachInnerBlockFunc())
            # Push Input.value to Form.inputData
            ->defineRelation('inputData', $prefix . 'value', function(&$data, $value) {
                if (!array_key_exists($this->name, $data)) {
                    $data[$this->name] = $value;
                }
            })
            # Read Input.value from Form.requestData
            ->defineRelation($prefix . 'value', 'requestData', function(&$value, $data) {
                if (array_key_exists($this->name, $data)) {
                    $value = $data[$this->name];
                }
            })
            ->defineRelation($prefix . 'errors', 'errors', function(&$inputErrors, $errors) {
                if (array_key_exists($this->name, $errors)) {
                    $inputErrors = $errors[$this->name];
                }
            })
        ;
//
//        $blockId = $this->name . 'InputBlock';
//        $hub->builder()
//            ->define($blockId, $this)
//            ->usedBy('formBlock', $this->attachInnerBlockFunc())
//            ->defineRelation('inputData', $blockId, function (&$inputData, AbstractInput $input) {
//                if (!array_key_exists($input->name, $inputData)) {
//                    $inputData[$input->name] = $input->value;
//                }
//            })
//            ->defineRelation($blockId, 'requestData', function (AbstractInput $input, $requestData) {
//                if (array_key_exists($input->name, $requestData)) {
//                    $input->value = $requestData[$input->name];
//                }
//            })
//            ->defineRelation($blockId, 'errors', function (AbstractInput $input, $errors) {
//                if (array_key_exists($input->name, $errors)) {
//                    $input->setErrors($errors[$input->name]);
//                }
//            })
//        ;

    }
}