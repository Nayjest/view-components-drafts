<?php

namespace ViewComponents\Core\Block\ListBlock;

use Exception;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\BlockComponentInterface;
use ViewComponents\Core\Block\Compound\Component\HandlersTrait;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Form\InputInterface;
use ViewComponents\Core\Block\Form\Select;
use ViewComponents\Core\Block\ListBlock;


class PageSizeSelect implements BlockComponentInterface
{
    use HandlersTrait;
    const ID = 'page_size_select';
    const EVENT_ID = 'update_page_size';

    private $block;

    public function __construct(InputInterface $input = null)
    {
        $this->block = $input;
    }

    public function getId()
    {
        return static::ID;
    }

    /**
     * @return InputInterface
     */
    public function getBlock()
    {
        if ($this->block === null) {
            $this->block = new Select('page_size', 'Page Size', [
                50 => 50,
                100 => 100,
                300 => 300,
                1000 => 1000
            ]);
        }
        return $this->block;
    }

    public function handle($eventId, Compound $root)
    {
        $this->checkRootType($eventId, $root, ListBlock::class);

        /** @var ListBlock $root */
        if ($eventId === Compound::EVENT_SET_ROOT) {
            $root->formBlock->addComponent($this->getBlock());
            $root->defineEvent(self::EVENT_ID)
                ->after(Form::EVENT_UPDATE_ERRORS)
                ->before(ListBlock::EVENT_MODIFY_QUERY);
        } elseif ($eventId === Compound::EVENT_UNSET_ROOT) {
            $root->formBlock->removeComponent($this->getBlock());
        } elseif ($eventId === static::EVENT_ID) {
            $this->updatePagination($root);
        }
    }

    /**
     * @return int|null
     */
    protected function getValue()
    {
        if ($this->getBlock()->hasErrors()) {
            return null;
        }
        $value = $this->getBlock()->getValue();
        if ($value === '' || $value === null) {
            return null;
        }
        return (int)$value;
    }

    protected function updatePagination(Compound $root)
    {
        $value = $this->getValue();
        if ($value === null) {
            return;
        }
        if (!$root->hasComponent(Pagination::ID)) {
            throw new Exception(self::class . ' used with block that has no pagination component.');
        }
        /** @var Pagination $pagination */
        $pagination = $root->getComponent(Pagination::ID);
        $pagination->setPageSize($value);
    }
}
