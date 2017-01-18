<?php

namespace ViewComponents\Core\Block\ListBlock;

use Exception;
use Nayjest\Querying\Operation\SortOperation;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
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
        $this->fieldSelect = new Select('sort_by', 'Sort by', $fields);
        $this->fieldSelect->containerBlock->setName('span');
        $this->directionSelect = new Select('sort_dir', null, array_combine($directions, $directions));
        $this->directionSelect->containerBlock->setName('span');
        if ($defaultField !== null) {
            $this->fieldSelect->setValue($defaultField);
        }
        if ($defaultDirection !== null) {
            $this->directionSelect->setValue($defaultDirection);
        }
        $this->container = new Tag('div');
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'sorting_select';
    }

    public function handle($eventId, Compound $root)
    {
        /** @var ListBlock $root */
        if ($eventId === Compound::EVENT_SET_ROOT) {
            if (!$root instanceof ListBlock) {
                throw new Exception("ListBlock expected");
            }
            $root->getFormBlock()->addComponents([
                new InnerBlock('form.sorting_select_container', $this->container),
                new InnerBlock('form.sorting_select_container.field_select', $this->fieldSelect),
                new InnerBlock('form.sorting_select_container.direction_select', $this->directionSelect),
            ]);
        } elseif ($eventId === ListBlock::EVENT_MODIFY_QUERY) {
            $field = $this->fieldSelect->getValue();
            if ($field === '' || $field === null) {
                return;
            }
            $root->getQuery()->addOperation(new SortOperation(
                $field,
                $this->directionSelect->getValue()
            ));
        }
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
