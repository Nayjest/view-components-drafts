<?php

namespace ViewComponents\Core\Block\ListBlock;

use Nayjest\Querying\Operation\FilterOperation;
use Nayjest\Querying\QueryInterface;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\BlockComponentInterface;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Compound\Component\HandlersTrait;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Form\Input;
use ViewComponents\Core\Block\Form\InputInterface;
use ViewComponents\Core\Block\Form\Select;
use ViewComponents\Core\Block\ListBlock;

class Filter implements BlockComponentInterface
{
    use HandlersTrait;
    /**
     * @var
     */
    private $fieldName;
    /**
     * @var
     */
    private $operator;

    private $id;

    private $parentId = 'form';

    /** @var InputInterface  */
    private $block;

    public static function inputName($fieldName, $operator)
    {
        $suffixes = [
            FilterOperation::OPERATOR_LIKE => '_pattern',
            FilterOperation::OPERATOR_STR_STARTS_WITH => '_start',
            FilterOperation::OPERATOR_STR_ENDS_WITH => '_end',
            FilterOperation::OPERATOR_STR_CONTAINS => '_contains',
            FilterOperation::OPERATOR_EQ => '',
            FilterOperation::OPERATOR_NOT_EQ => '_not',
            FilterOperation::OPERATOR_GT => '_gt',
            FilterOperation::OPERATOR_LT => '_lt',
            FilterOperation::OPERATOR_GTE => '_gte',
            FilterOperation::OPERATOR_LTE => '_lte',
        ];
        $suffix = isset($suffixes[$operator]) ? $suffixes[$operator] : '';
        return $fieldName . $suffix;
    }

    public static function makeWithSelect($fieldName, array $options, $operator = FilterOperation::OPERATOR_EQ)
    {
        return new self(
            $fieldName,
            $operator,
            new Select(
                static::inputName($fieldName, $operator),
                self::makeInputLabel($fieldName, $operator),
                $options
            )
        );
    }

    public function __construct($fieldName, $operator, InputInterface $input = null)
    {
        $this->fieldName = $fieldName;
        $this->operator = $operator;
        $this->id = $this->fieldName . $operator. '_filter';
        $this->block = $input;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParentId($id)
    {
        $this->parentId = $id;
        return $this;
    }

    /**
     * @return InputInterface
     */
    public function getBlock()
    {
        if ($this->block === null) {
            $this->block = new Input(
                static::inputName($this->fieldName, $this->operator),
                static::makeInputLabel($this->fieldName, $this->operator)
            );
        }
        return $this->block;
    }

    public function handle($eventId, Compound $root)
    {
        /** @var ListBlock $root */
        $this->checkRootType($eventId, $root, ListBlock::class);
        if ($eventId === Compound::EVENT_SET_ROOT) {
            $root->formBlock->addComponent($this->getBlock());
        } elseif ($eventId === Compound::EVENT_UNSET_ROOT) {
            $root->formBlock->removeComponent($this->getBlock()->getId());
        } elseif ($eventId === ListBlock::EVENT_MODIFY_QUERY) {
            $this->modifyQuery($root->getQuery());
        }
    }

    protected function modifyQuery(QueryInterface $query)
    {
        if ($this->getBlock()->hasErrors()) {
            return;
        }
        $value = $this->getBlock()->getValue();
        if ($value === '' || $value === null) {
            return;
        }
        $query->addOperation(new FilterOperation(
            $this->fieldName,
            $this->operator,
            $value
        ));
    }

    protected static function makeInputLabel($fieldName, $operator)
    {
        $suffixes = [
            FilterOperation::OPERATOR_LIKE => ' Pattern',
            FilterOperation::OPERATOR_STR_STARTS_WITH => ' Characters',
            FilterOperation::OPERATOR_STR_ENDS_WITH => ' Characters',
            FilterOperation::OPERATOR_STR_CONTAINS => ' Part',
            FilterOperation::OPERATOR_EQ => '',
            FilterOperation::OPERATOR_NOT_EQ => ' Not Eq.',
            FilterOperation::OPERATOR_GT => ' Greater Than',
            FilterOperation::OPERATOR_LT => 'Less Than',
            FilterOperation::OPERATOR_GTE => '',
            FilterOperation::OPERATOR_LTE => '',
        ];
        $prefixes = [

            FilterOperation::OPERATOR_LIKE => '',
            FilterOperation::OPERATOR_STR_STARTS_WITH => 'First ',
            FilterOperation::OPERATOR_STR_ENDS_WITH => 'Last ',
            FilterOperation::OPERATOR_EQ => '',
            FilterOperation::OPERATOR_NOT_EQ => '',
            FilterOperation::OPERATOR_GT => '',
            FilterOperation::OPERATOR_LT => '',
            FilterOperation::OPERATOR_GTE => 'Min. ',
            FilterOperation::OPERATOR_LTE => 'Max. ',
        ];
        $suffix = isset($suffixes[$operator]) ? $suffixes[$operator] : '';
        $prefix = isset($prefixes[$operator]) ? $prefixes[$operator] : '';
        return $prefix . ucwords(str_replace(array('-', '_'), ' ', $fieldName)) . $suffix;
    }
}