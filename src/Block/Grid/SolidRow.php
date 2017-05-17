<?php

namespace ViewComponents\Grids\Component;

use Nayjest\DI\Definition\Relation;
use Nayjest\DI\Definition\Value;
use Nayjest\DI\SubHub;
use ViewComponents\Core\Block\Compound\Component\AbstractComponent;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Compound\Component\InnerBlock\InnerBlockRelation;
use ViewComponents\Core\Block\Grid;
use ViewComponents\Core\Block\ListBlock;
use ViewComponents\Core\Block\Tag;

/**
 * Table row containing one cell with colspan attribute equal to grid's columns count.
 *
 * This component hides it's internal structure from accessing via children() method and provides direct access to
 * it's "cell" component children.
 *
 */
class SolidRow extends InnerBlock
{
    public function  getId()
    {
        return $this->hub->get('parentId') . 'SolidRow';
    }

    /**
     * SolidRow constructor.
     *
     * If columns count argument is specified, SolidRow will use it instead of real grid's columns count.
     *
     * @param string $parentId
     * @param string $cellTagName 'td'|'th'
     * @param int|null $columnCount
     */
    public function __construct($parentId, $cellTagName = 'td', $columnCount = null)
    {
        $data = [
            new Value('parentId', $parentId, Value::FLAG_READONLY),
            'cellTag' => Tag::make($cellTagName, $columnCount ? ['colspan' => $columnCount] : []),
            'rowTag' => new Tag('tr', [], [$this->cellTag]),
            new InnerBlockRelation(SubHub::externalItemId($parentId), 'rowTag'),
        ];
        if ($columnCount === null) {
            $data[] = new Relation('cellTag', SubHub::externalItemId(Grid::COLUMNS), function(Tag &$cellTag, array $columns) {
                $cellTag->setAttribute('colspan', count($columns));
            });
        }
        parent::__construct($id, $parentId, $vlock);
    }
}
