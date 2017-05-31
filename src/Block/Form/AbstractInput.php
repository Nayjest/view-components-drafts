<?php

namespace ViewComponents\Core\Block\Form;

use Nayjest\DI\Definition\Relation;
use Nayjest\DI\HubInterface;
use ViewComponents\Core\Block\CollectionPresenter;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\AbstractCompoundComponent;
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
class AbstractInput extends AbstractCompoundComponent
{
    const LABEL_BLOCK = 'labelBlock';
    const ERRORS_CONTAINER_BLOCK = 'errorsContainerBlock';
    const ERROR_COLLECTION_BLOCK = 'errorCollectionBlock';
    const ERROR_BLOCK = 'errorBlock';

    const ERRORS = 'errors';
    const NAME = 'name';
    const LABEL = 'label';
    const VALUE = 'value';

    protected function getId()
    {
        return $this->name . 'Input';
    }

    public function __construct($name, $label = null, $value = null)
    {
        parent::__construct(compact(self::NAME, self::LABEL, self::VALUE) + [
            'parentId' => Form::FORM_BLOCK,
            self::ERRORS => [],

            // replace empty container to 'div' tag
            Compound::CONTAINER_BLOCK => Tag::make('div')->setBlockSeparator(' '),

            InnerBlock::make(self::LABEL_BLOCK, Tag::make('label')->setSortPosition(1)),
            new Relation(self::LABEL_BLOCK, self::LABEL, function (Tag $block, $label) {
                $block
                    ->setVisibility($label !== null)
                    ->setData($label);
            }),

            InnerBlock::make(self::ERRORS_CONTAINER_BLOCK, Tag::make('div')->setSortPosition(3)),
            new Relation(self::ERRORS_CONTAINER_BLOCK, self::ERRORS, function (Tag $block, $errors) {
                $block->setVisibility(!empty($errors));
            }),
            new InnerBlock(self::ERROR_COLLECTION_BLOCK, self::ERRORS_CONTAINER_BLOCK, new CollectionPresenter()),

            new Relation(self::ERROR_COLLECTION_BLOCK, self::ERRORS, function (CollectionPresenter $block, $errors) {
                $block->setData($errors);
            }),
            new InnerBlock(self::ERROR_BLOCK, self::ERROR_COLLECTION_BLOCK, new Tag('p')),
            new Relation(self::ERROR_COLLECTION_BLOCK, self::ERROR_BLOCK, function (CollectionPresenter $block, $errorBlock) {
                $block->setRecordView($errorBlock);
            }),

        ]);
    }

    public function hasErrors()
    {
        return !empty($this->hub->get(self::ERRORS));
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
        parent::register($hub);
        $hub->addDefinitions([
            # Attach to Form.formBlock as inner block
            new InnerBlock\InnerBlockRelation($this->parentId, $this->externalId(self::ROOT_BLOCK)),

            # Push Input.value to Form.inputData
            new Relation(
                Form::INPUT_DATA,
                $this->externalId(self::VALUE),
                function(&$data, $value) {
                    if (!array_key_exists($this->name, $data)) {
                        $data[$this->name] = $value;
                    }
                }
            ),

            # Read Input.value from Form.requestData
            new Relation($this->externalId(self::VALUE), Form::REQUEST_DATA, function(&$value, $data) {
                if (array_key_exists($this->name, $data)) {
                    $value = $data[$this->name];
                }
            }),

            new Relation($this->externalId(self::ERRORS), Form::ERRORS, function(&$inputErrors, $errors) {
                if (array_key_exists($this->name, $errors)) {
                    $inputErrors = $errors[$this->name];
                }
            })
        ]);
    }
}