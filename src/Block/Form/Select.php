<?php

namespace ViewComponents\Core\Block\Form;

use ViewComponents\Core\Block\Block;
use ViewComponents\Core\Block\CollectionPresenter;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\DataPresenter;
use ViewComponents\Core\Block\Tag;
use ViewComponents\Core\Compound\Component;
use ViewComponents\Core\DataPresenterInterface;

/**
 * Class Select
 *
 * @property Tag $selectBlock
 * @method Tag getSelectBlock()
 * @method $this setSelectBlock(Tag $block)
 *
 * @property CollectionPresenter $optionCollectionBlock
 * @method CollectionPresenter getOptionCollectionBlock()
 * @method $this setOptionCollectionBlock(CollectionPresenter $block)
 *
 * @property DataPresenterInterface $optionBlock
 * @method DataPresenterInterface getOptionBlock()
 * @method $this setOptionBlock(DataPresenterInterface $block)
 *
 */
class Select extends AbstractInput
{
    /**
     * @var array
     */
    private $options;

    public function __construct($name, $label = null, array $options = [])
    {
        parent::__construct($name, $label, null);
        $this->addComponents([
            new InnerBlock('container.select', Tag::make('select')->setSortPosition(2), function () {
                $this->selectBlock->setAttribute('name', $this->name);
            }),
            new InnerBlock('select.option_collection', new CollectionPresenter(), function () {
                $this->optionCollectionBlock
                    ->setData($this->getOptionsForSelect())
                    ->setRecordView($this->optionBlock);
            }),
            new InnerBlock(
                'option_collection.option',
                new DataPresenter(
                    function (array $record, Tag $optionBlock) {
                        $optionBlock
                            ->setData($record['label'])
                            ->setAttribute('value', $record['value']);
                        if ($record['value'] == $this->value) {
                            $optionBlock->setAttribute('selected', 'selected');
                        } elseif ($optionBlock->hasAttribute('selected')) {
                            $optionBlock->removeAttribute('selected');
                        }
                    },
                    Tag::make('option')
                )
            ),
        ]);
        $this->options = $options;
    }

    protected function getOptionsForSelect()
    {
        $optionsForSelect = [];
        foreach ($this->options as $key => $value) {
            if (!is_array($value)) {
                $option = [
                    'value' => $key,
                    'label' => $value
                ];
            } else {
                $option = $value;
            }
            $optionsForSelect[] = $option;
        }
        return $optionsForSelect;
    }
}