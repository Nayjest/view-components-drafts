<?php

namespace ViewComponents\Core\Block\Form;

use ViewComponents\Core\Block\CollectionPresenter;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\DataPresenter;
use ViewComponents\Core\Block\Tag;
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
    const SELECT_BLOCK = 'selectBlock';
    const OPTION_COLLECTION_BLOCK = 'optionCollectionBlock';
    const OPTION_BLOCK = 'optionBlock';

    const OPTIONS = 'options';

    public function __construct($name, $label = null, array $options = [])
    {
        parent::__construct($name, $label, null);
        $this->addComponents([
            new InnerBlock(self::SELECT_BLOCK, null, Tag::make('select')->setSortPosition(2)),
            new InnerBlock(self::OPTION_COLLECTION_BLOCK, self::SELECT_BLOCK, new CollectionPresenter()),
            new InnerBlock(
                self::OPTION_BLOCK,
                self::OPTION_COLLECTION_BLOCK,
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

        $this->hub->builder()
            ->define(self::OPTIONS, $options)
            ->usedBy(self::OPTION_COLLECTION_BLOCK, function (CollectionPresenter $block, $options) {
                $block->setData($this->getOptionsForSelect($options));
            })
            ->usedBy(self::VALUE, function (&$value, $options) {
                if ($value === null) {
                    $options = $this->getOptionsForSelect($options);
                    foreach ($options as $option) {
                        $value = $option['value'];
                        return;
                    }
                }
            })
            ->defineRelation(self::SELECT_BLOCK, self::NAME, function (Tag $select, $name) {
                $select->setAttribute('name', $name);
            })
            ->defineRelation(self::OPTION_COLLECTION_BLOCK, self::OPTION_BLOCK, function (CollectionPresenter $collection, DataPresenterInterface $option) {
                $collection->setRecordView($option);
            });
    }

    protected function getOptionsForSelect(array $options)
    {
        $optionsForSelect = [];
        foreach ($options as $key => $value) {
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