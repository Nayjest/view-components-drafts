<?php

namespace ViewComponents\Core\Block\ListBlock;

use Exception;
use Nayjest\Querying\Operation\PaginateOperation;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\BlockComponentInterface;
use ViewComponents\Core\Block\Compound\Component\BlockComponentTrait;
use ViewComponents\Core\Block\Compound\Component\HandlersTrait;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\ListBlock;
use ViewComponents\Core\Block\ListBlock\Pagination\PaginationTemplate;
use ViewComponents\Core\Block\ListBlock\Pagination\PaginationViewInterface;

class Pagination implements BlockComponentInterface
{
    const ID = 'pagination';
    const DEFAULT_URI_KEY = 'p';
    const EVENT_PREPARE_PAGINATION_VIEW = 'prepare_pagination_view';

    use BlockComponentTrait {
        BlockComponentTrait::handle as private handleInternal;
    }
    use HandlersTrait;

    const SORT_POSITION = 9;
    /**
     * @var int
     */
    protected $pageSize;

    /**
     * @var string
     */
    private $uriPageParam;

    private $block;


    public function __construct(
        $uriPageParam = self::DEFAULT_URI_KEY,
        $pageSize = 50,
        $parentId = null
    )
    {
        $this->pageSize = $pageSize;
        $this->uriPageParam = $uriPageParam;
        $this->parentId = $parentId;
    }

    public function getId()
    {
        return self::ID;
    }

    /**
     * @return PaginationViewInterface
     */
    public function getBlock()
    {
        if ($this->block === null) {
            $this->block = new PaginationTemplate();
        }
        return $this->block;
    }

    public function setBlock(PaginationViewInterface $block)
    {
        $this->block = $block;
    }

    public function handle($eventId, Compound $root)
    {
        $this->checkRootType($eventId, $root, ListBlock::class);
        /** @var ListBlock $root */
        if ($eventId === Compound::EVENT_SET_ROOT) {
            $root->defineEvent(self::EVENT_PREPARE_PAGINATION_VIEW)
                ->after(ListBlock::EVENT_EXECUTE_QUERY)
                ->before(ListBlock::EVENT_FINALIZE);
        } elseif ($eventId === ListBlock::EVENT_MODIFY_QUERY) {
            $root->getQuery()->addOperation(new PaginateOperation(
                $this->getCurrentPage($root),
                $this->pageSize
            ));
        } elseif ($eventId === self::EVENT_PREPARE_PAGINATION_VIEW) {
            $this->getBlock()
                ->setCurrent($this->getCurrentPage($root))
                ->setUriKey($this->uriPageParam)
                ->setTotal($this->getPageCount($root))
                ->setSortPosition(self::SORT_POSITION);
        }
        $this->handleInternal($eventId, $root);
    }

    protected function getCurrentPage(ListBlock $root)
    {
        return $root->getFormBlock()->getInputValue($this->uriPageParam)?:1;
    }

    protected function getPageCount(ListBlock $root)
    {
        return (int)ceil($this->getTotalRecordsCount($root) / $this->pageSize);
    }

    /**
     * @param ListBlock $root
     * @return int
     * @throws Exception
     */
    protected function getTotalRecordsCount(ListBlock $root)
    {
        $query = $root->getQuery();
        $removed = [];
        foreach ($query->getOperations() as $operation) {
            if ($operation instanceof PaginateOperation) {
                $removed[] = $operation;
                $query->removeOperation($operation);
            }
        }
        $count = $query->count();
        $query->addOperations($removed);
        return $count;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * Sets page size.
     *
     * @param int $pageSize
     * @return Pagination
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }
}
