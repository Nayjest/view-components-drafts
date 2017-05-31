<?php

namespace ViewComponents\Core\Block\Grid;

use Nayjest\DI\Definition\Item;

use Nayjest\DI\Definition\Relation;
use Nayjest\DI\Definition\Value;
use Nayjest\DI\SubHub;
use Nayjest\Querying\Row\RowInterface;
use ViewComponents\Core\Block\Block;
use ViewComponents\Core\Block\Compound\Component\AbstractComponent;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Compound\Component\InnerBlock\InnerBlockRelation;
use ViewComponents\Core\Block\Grid;
use ViewComponents\Core\Block\Tag;

/**
 * Class Column
 *
 * @property-read string $id
 * @property string $label
 * @property string $dataField
 *
 * @property Tag $titleCellBlock
 * @property Tag $dataCellBlock
 * @property Block $titleTextBlock
 * @property Block $dataTextBlock
 */
class Column extends AbstractComponent
{
    const TITLE_CELL_BLOCK = 'titleCellBlock';
    const DATA_CELL_BLOCK = 'dataCellBlock';
    const TITLE_TEXT_BLOCK = 'titleTextBlock';
    const DATA_TEXT_BLOCK = 'dataTextBlock';
    const DATA_FIELD = 'dataField';

    protected function getId()
    {
        return $this->id . 'Column';
    }

    /**
     * Column constructor.
     *
     * @param string $id
     * @param string|null $label
     */
    public function __construct($id, $label = null)
    {
        parent::__construct([
            new Value('id', $id, Value::FLAG_READONLY),
            new Value('label', $label),
            new Value('dataField', $id),
            self::TITLE_CELL_BLOCK => new Tag('th'),
            self::DATA_CELL_BLOCK => new Tag('td'),
            new Item(self::TITLE_TEXT_BLOCK, 'label', function (Block &$block = null, $label) {
                if ($block === null) {
                    $block = new Block();
                }
                $block->setData($label);
            }),
            self::DATA_TEXT_BLOCK => new Block(),
            new InnerBlockRelation(self::TITLE_CELL_BLOCK, self::TITLE_TEXT_BLOCK),
            new InnerBlockRelation(self::DATA_CELL_BLOCK, self::DATA_TEXT_BLOCK),
            new InnerBlockRelation(SubHub::externalItemId(Grid::TABLE_HEADING_BLOCK), self::TITLE_CELL_BLOCK),
            new InnerBlockRelation(SubHub::externalItemId(Grid::TABLE_ROW_BLOCK), self::DATA_CELL_BLOCK),
            new Relation(
                SubHub::externalItemId(Grid::DATA_INJECTORS),
                self::DATA_FIELD,
                function (array &$injectors, $dataField) {
                    $injectors[$this->id] = function (RowInterface $row) use ($dataField) {
                        $this->dataTextBlock->setData($row->get($dataField));
                    };
                }
            ),
            new Relation(Grid::COLUMNS, null, function(array &$columns) use($id) {
                $columns[$id] = $this;
            })
        ]);
    }
}