<?php

namespace ViewComponents\Core\Block;

use Nayjest\DI\Definition\Item;
use Nayjest\DI\Definition\Relation;
use Nayjest\DI\Definition\Value;
use Nayjest\Querying\AbstractQuery;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\ListBlock\RecordView;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Form\RequestData;
use ViewComponents\Core\DataPresenterInterface;

/**
 * Class ListBlock
 *
 * @property CollectionPresenter $collectionBlock
 * @method CollectionPresenter getCollectionBlock()
 * @method $this setCollectionBlock(CollectionPresenter $block)
 *
 * @property \ViewComponents\Core\Block\Form $formBlock
 * @method Form getFormBlock()
 * @method $this setFormBlock(Form $block)
 *
 * @property DataPresenterInterface $recordViewBlock
 * @method DataPresenterInterface getRecordViewBlock()
 * @method $this setRecordViewBlock(DataPresenterInterface $block)
 */
class ListBlock extends Compound
{
    const FORM_ID = 'form';
    const QUERY = 'query';
    const DATA = 'data';

    const RECORD_VIEW_BLOCK = 'recordViewBlock';
    const FORM_BLOCK = 'formBlock';
    const COLLECTION_BLOCK = 'collectionBlock';

    const FLAG_USE_RAW_DATA = 1;

    public function __construct(AbstractQuery $query, array $components = [], $flags = self::FLAG_USE_RAW_DATA)
    {
        parent::__construct($components);
        $this->hub->addDefinitions([
            new Value('useRawData', $flags & self::FLAG_USE_RAW_DATA),
            new Value(self::QUERY, $query),
            new Item(self::DATA, self::QUERY, function(&$data, AbstractQuery $query) {
                $data = $this->useRawData ? $query->getRaw() : $query->get();
            }),
            new Relation(self::COLLECTION_BLOCK, self::DATA, function (CollectionPresenter $collectionBlock, $data) {
                $collectionBlock->setData($data);
            }),
            new Relation(
                self::COLLECTION_BLOCK,
                self::RECORD_VIEW_BLOCK,
                function (CollectionPresenter &$collectionBlock, DataPresenterInterface $recordView) {
                    $collectionBlock->setRecordView($recordView);
                }
            )
        ]);
        $this->initializeDefaultComponents();
    }

    /**
     * @return ComponentInterface[]
     */
    protected function getDefaultComponents()
    {
        $form = new Form([
            new RequestData($_GET),
            new InnerBlock(
                Form::SUBMIT_BUTTON_BLOCK,
                Form::FORM_BLOCK,
                Tag::make(
                    'button',
                    ['type' => 'submit'],
                    [new Block('Refresh')]
                )->setSortPosition(Form::SUBMIT_SORT_POSITION)
            )
        ]);

        return [
            self::FORM_BLOCK => InnerBlock::make(
                self::FORM_BLOCK,
                $form
            ),
            self::COLLECTION_BLOCK => InnerBlock::make(
                self::COLLECTION_BLOCK,
                (new CollectionPresenter())->setSortPosition(2)
            ),
            self::RECORD_VIEW_BLOCK => new RecordView(),
        ];
    }
    protected function initializeDefaultComponents()
    {
        foreach($this->getDefaultComponents() as $id => $component) {
            if (!$this->hub->has($id)) {
                $this->addComponent($component);
            }
        }
    }
}

