<?php

namespace ViewComponents\Core\Block\ListBlock;

use Nayjest\DI\Definition\Relation;
use Nayjest\DI\HubInterface;
use Nayjest\Querying\Operation\SortOperation;
use Nayjest\Querying\QueryInterface;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Form\Select;
use ViewComponents\Core\Block\ListBlock;
use ViewComponents\Core\Block\Tag;

/**
 * Class SortingControl
 *
 */
class SortingControl implements ComponentInterface
{
    /**
     * @var array
     */
    protected $fields;

    /** @var  Select */
    protected $fieldSelect;

    /** @var  Select */
    protected $directionSelect;

    protected $container;

    public function __construct(array $fields, $defaultField = null, $defaultDirection = SortOperation::ASC)
    {
        $directions = [
            SortOperation::ASC,
            SortOperation::DESC
        ];
        $this->fieldSelect = new Select('sort_by', 'Sort by', array_combine($fields, $fields));
        $this->fieldSelect->containerBlock->setName('span');
        $this->directionSelect = new Select('sort_dir', null, array_combine($directions, $directions));
        $this->directionSelect->containerBlock->setName('span');
        if ($defaultField !== null) {
            $this->fieldSelect->setValue($defaultField);
        }
        if ($defaultDirection !== null) {
            $this->directionSelect->setValue($defaultDirection);
        }
        $this->container = new Tag('span');
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'sorting_select';
    }

    public function register(HubInterface $hub)
    {
        $this->fieldSelect->parentId = 'sortingSelectContainerBlock';
        $this->directionSelect->parentId = 'sortingSelectContainerBlock';
        $hub->addDefinitions([
            new Relation(ListBlock::FORM_BLOCK, null, function(Form $form) {
                $form->addComponents([
                    InnerBlock::make('form.sortingSelectContainer', $this->container),
                    $this->fieldSelect,
                    $this->directionSelect,
                ]);
            }),
            new Relation(ListBlock::QUERY, null, function(QueryInterface $query) {
                $field = $this->fieldSelect->getValue();
                if ($field === '' || $field === null) {
                    return;
                }
                $query->addOperation(new SortOperation(
                    $field,
                    $this->directionSelect->getValue()
                ));
            }),
        ]);
    }

    /**
     * @return Select
     */
    public function getFieldSelectBlock()
    {
        return $this->fieldSelect;
    }

    /**
     * @return Select
     */
    public function getDirectionSelectBlock()
    {
        return $this->directionSelect;
    }

    /**
     * @return Tag
     */
    public function getContainer()
    {
        return $this->container;
    }
}
