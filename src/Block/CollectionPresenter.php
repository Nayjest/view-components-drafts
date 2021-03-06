<?php

namespace ViewComponents\Core\Block;

use ViewComponents\Core\AbstractContainer;
use ViewComponents\Core\DataPresenterInterface;
use ViewComponents\Core\DataPresenterTrait;

class CollectionPresenter extends AbstractContainer implements DataPresenterInterface
{
    use DataPresenterTrait;

    /**
     * @var DataPresenterInterface
     */
    private $recordView;

    public function __construct(DataPresenterInterface $recordView = null, array $data = [])
    {
        $this->setData($data);
        $this->recordView = $recordView;
    }

    /**
     * @return DataPresenterInterface
     */
    public function getRecordView()
    {
        if ($this->recordView === null) {
            $this->recordView = new VarDump();
        }
        return $this->recordView;
    }

    /**
     * @param DataPresenterInterface $recordView
     * @return $this
     */
    public function setRecordView(DataPresenterInterface $recordView)
    {
        $this->recordView = $recordView;
        return $this;
    }

    protected function renderInternal()
    {
        $recordView = $this->getRecordView();
        if (!in_array($recordView, $this->getInnerBlocksRecursive())) {
            $this->addInnerBlock($recordView);
        }
        $out = '';

        foreach ($this->getData() as $record) {
            $recordView->setData($record);
            $out .= $this->renderInnerBlocks();
        }
        return $out;
    }
}
