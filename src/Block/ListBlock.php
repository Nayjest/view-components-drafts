<?php

namespace ViewComponents\Core\Block;

use Nayjest\Querying\AbstractQuery;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\BlockInterface;
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
    const QUERY = 'query';
    const DATA = 'data';
    const RECORD_VIEW_BLOCK = 'recordViewBlock';
    const FORM_BLOCK = 'formBlock';
    const COLLECTION_BLOCK = 'collectionBlock';

    public function __construct(AbstractQuery $query, BlockInterface $recordView = null, array $components = [])
    {
        parent::__construct($components);
        $this->hub->builder()
            ->define(self::QUERY, $query)
            ->define(self::DATA, null, true)
            ->uses(self::QUERY, function(&$data, AbstractQuery $query) {
                $data = $query->getRaw();
            })
            ->usedBy(self::COLLECTION_BLOCK, function(CollectionPresenter $collectionBlock, $data) {
                $collectionBlock->setData($data);
            })
            ->defineRelation(
                self::COLLECTION_BLOCK,
                self::RECORD_VIEW_BLOCK,
                function(CollectionPresenter $collectionBlock, DataPresenterInterface $recordView){
                    $collectionBlock->setRecordView($recordView);
                 }
            )
        ;
        if (!$this->hub->has(self::FORM_BLOCK)) {
            $this->addComponent(
                new InnerBlock(
                    'form',
                    $form = Form::make([new RequestData($_GET)])->setSortPosition(2)
                )
            );
        }
        if(!$this->hub->has(self::COLLECTION_BLOCK)) {
            $this->addComponent(new InnerBlock('collection', (new CollectionPresenter())->setSortPosition(2)));
        }
        if(!$this->hub->has(self::RECORD_VIEW_BLOCK)) {
            $this->addComponent(new InnerBlock('collection.recordView', $recordView ?: new VarDump()));
        }
    }
}

