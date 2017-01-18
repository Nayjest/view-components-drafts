<?php

namespace ViewComponents\Core\Block;

use Nayjest\Querying\AbstractQuery;
use ViewComponents\Core\Block\Compound\Component\Event;
use ViewComponents\Core\Block\Compound\Component\Handler;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\BlockInterface;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Form\RequestData;
use ViewComponents\Core\Compound\CompoundMagicTrait;
use ViewComponents\Core\DataPresenterInterface;
use ViewComponents\Core\DataPresenterTrait;

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
    use DataPresenterTrait;
    use CompoundMagicTrait;

    const EVENT_MODIFY_QUERY = 'modify_query';
    const EVENT_EXECUTE_QUERY = 'execute_query';

    public function __construct(AbstractQuery $query, BlockInterface $recordView = null, array $components = [])
    {
        $this->defineEvent(self::EVENT_MODIFY_QUERY)
            ->after(Form::EVENT_UPDATE_ERRORS)
            ->before(Compound::EVENT_FINALIZE);
        $this->defineEvent(self::EVENT_EXECUTE_QUERY)
            ->after(self::EVENT_MODIFY_QUERY)
            ->before(Compound::EVENT_FINALIZE);
        $this
            ->setData($query)
            ->addComponents([
                new InnerBlock(
                    'form',
                    $form = Form::make([new RequestData($_GET)])->setSortPosition(2)
                ),
                new InnerBlock('collection', (new CollectionPresenter())->setSortPosition(2)),
                new InnerBlock('collection.record_view', $recordView ?: new VarDump()),
                new Handler(self::EVENT_EXECUTE_QUERY, function() {
                    $this->collectionBlock
                        ->setData($this->getFinalData())
                        ->setRecordView($this->recordViewBlock);
                })
            ])
            ->addComponents($components);
    }

    /**
     * @return AbstractQuery
     */
    public function getQuery()
    {
        return $this->getData();
    }

    protected function getFinalData()
    {
        return $this->getQuery()->getRaw();
    }
}

