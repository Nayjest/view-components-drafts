<?php

namespace ViewComponents\Core\Block\ListBlock;

use ViewComponents\Core\Block\Compound\Component\AbstractComponent;
use ViewComponents\Core\Block\ListBlock;
use ViewComponents\Core\Block\VarDump;
use ViewComponents\Core\DataPresenterInterface;

class RecordView extends AbstractComponent
{
    public function getId()
    {
        return 'recordView';
    }

    public function __construct(DataPresenterInterface $block = null)
    {
        if ($block === null) {
            $block = new VarDump();
        }
        parent::__construct(compact('block'));
    }
}