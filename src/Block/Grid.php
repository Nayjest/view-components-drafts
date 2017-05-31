<?php

namespace ViewComponents\Core\Block;

use Nayjest\DI\Definition\Value;
use Nayjest\Querying\AbstractQuery;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\ListBlock\RecordView;

class Grid extends ListBlock
{
    const TABLE_BLOCK = 'tableBlock';
    const TABLE_HEADING_BLOCK = 'tableHeadingBlock';
    const TABLE_FOOTER_BLOCK = 'tableFooterBlock';
    const TABLE_BODY_BLOCK = 'tableBodyBlock';
    const TITLE_ROW_BLOCK = 'titleRowBlock';
    const TABLE_ROW_BLOCK = 'tableRowBlock';

    const DATA_INJECTORS = 'dataInjectors';
    const COLUMNS = 'columns';

    public function __construct(AbstractQuery $query, array $components = [])
    {
        parent::__construct($query, $components, null);

        $this->hub->addDefinitions([
            new Value(self::DATA_INJECTORS, []),
            new Value(self::COLUMNS, [])
        ]);
    }

    protected function getDefaultComponents()
    {
        return array_merge(parent::getDefaultComponents(), [
            self::TABLE_HEADING_BLOCK => new InnerBlock(self::TABLE_HEADING_BLOCK, self::TABLE_BLOCK, new Tag('thead')),
            self::TABLE_BODY_BLOCK => new InnerBlock(self::TABLE_BODY_BLOCK, self::TABLE_BLOCK, new Tag('tbody')),
            self::TABLE_FOOTER_BLOCK => new InnerBlock(self::TABLE_FOOTER_BLOCK, self::TABLE_BLOCK, new Tag('tfoot')),
            self::TABLE_BLOCK => new InnerBlock(self::TABLE_BLOCK, null, (new Tag('table'))->setSortPosition(5)),
            self::COLLECTION_BLOCK => new InnerBlock(
                self::COLLECTION_BLOCK,
                self::TABLE_BODY_BLOCK,
                (new CollectionPresenter())->setSortPosition(2)
            ),
            self::TABLE_ROW_BLOCK => new InnerBlock(self::TABLE_ROW_BLOCK, self::COLLECTION_BLOCK, new Tag('tr')),
            self::RECORD_VIEW_BLOCK => new RecordView(
                new DataPresenter(function () {
                    foreach ($this->hub->get(self::DATA_INJECTORS) as $handler) {
                        call_user_func_array($handler, func_get_args());
                    }
                })
            )

        ]);
    }
}