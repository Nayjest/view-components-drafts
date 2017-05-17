<?php

namespace ViewComponents\Core\Block\Grid;

use Nayjest\DI\Definition\Item;
use Nayjest\DI\Definition\Relation;
use Nayjest\DI\Definition\Value;
use Nayjest\DI\HubInterface;
use Nayjest\DI\SubHub;
use Nayjest\Querying\Operation\OperationInterface;
use Nayjest\Querying\Operation\SortOperation;
use Nayjest\Querying\QueryInterface;
use ViewComponents\Core\Block\Compound\Component\AbstractCompoundComponent;
use ViewComponents\Core\Block\Compound\Component\InnerBlock\InnerBlockRelation;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Grid;
use ViewComponents\Core\Block\Template;
use ViewComponents\Core\Common\UriFunctions;
use ViewComponents\Core\DataPresenterInterface;


/**
 * ColumnSortingControl adds buttons for sorting grid data by specified column.
 * It's automatically placed after title of target column.
 *
 * @property DataPresenterInterface $block
 */
class ColumnSortingControl extends AbstractCompoundComponent
{
    const COLUMN_ID = 'columnId';
    const DIRECTION = 'direction';
    const OPERATION = 'operation';
    const BLOCK = 'block';

    protected function getId()
    {
        return $this->columnId . 'SortingControl';
    }

    /**
     * ColumnSortingControl constructor.
     *
     * @param string $columnId
     * @param DataPresenterInterface|null $block
     */
    public function __construct($columnId, DataPresenterInterface $block = null)
    {
        parent::__construct([
            new Value(self::COLUMN_ID, $columnId, Value::FLAG_READONLY),
            new Value(self::OPERATION, null, Value::FLAG_READONLY),
            new Value(self::BLOCK, $block ?: new Template('grid/column_sorting')),
            new Item(self::DIRECTION, SubHub::externalItemId(Grid::FORM_BLOCK),
                function (&$direction, Form $form) {
                    $direction = $form->getInputValue('sort_dir', null);
                }
            ),
            new Relation(
                SubHub::externalItemId(Grid::QUERY),
                self::OPERATION,
                function (QueryInterface $query, OperationInterface $operation = null, OperationInterface $prev = null) {
                    if ($prev) {
                        $query->removeOperation($prev);
                    }
                    if ($operation) {
                        $query->addOperation($operation);
                    }
                }
            ),
            new Relation(
                self::OPERATION,
                [
                    self::DIRECTION,
                    SubHub::externalItemId($columnId . 'ColumnComponent'),
                    SubHub::externalItemId(Grid::FORM_BLOCK),
                ],
                function (&$operation, $direction, Column $column, Form $form) {
                    if (
                        $column->dataField !== $form->getInputValue('sort_by')
                        || $direction === null
                    ) {
                        $operation = null;
                    } else {
                        $operation = new SortOperation(
                            $column->dataField,
                            $direction
                        );
                    }
                }

            ),
            new Relation(
                self::BLOCK,
                [
                    self::DIRECTION,
                    SubHub::externalItemId($columnId . 'ColumnComponent')
                ],
                function (DataPresenterInterface &$block, $direction, Column $column) {
                    $block->setData([
                        'order' => $direction,
                        'links' => $this->makeLinks($column->dataField)
                    ]);
                }
            ),
            new InnerBlockRelation(SubHub::externalItemId($columnId . 'ColumnTitleCellBlock'), self::BLOCK),
        ]);
    }

    protected function makeLinks($dataFiled)
    {
        $asc = UriFunctions::modifyQuery(
            null,
            [
                'sort_dir' => SortOperation::ASC,
                'sort_by' => $dataFiled
            ]
        );
        $desc = UriFunctions::modifyQuery(
            null,
            [
                'sort_dir' => SortOperation::DESC,
                'sort_by' => $dataFiled
            ]
        );
        return compact('asc', 'desc');
    }
}
