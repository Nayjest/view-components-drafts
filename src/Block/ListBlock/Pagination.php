<?php

namespace ViewComponents\Core\Block\ListBlock;

use Nayjest\DI\Definition\Item;
use Nayjest\DI\Definition\Relation;
use Nayjest\DI\Hub;
use Nayjest\DI\HubInterface;
use Nayjest\DI\SubHub;
use Nayjest\Querying\Operation\PaginateOperation;
use Nayjest\Querying\QueryInterface;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\ListBlock;
use ViewComponents\Core\Block\ListBlock\Pagination\PaginationTemplate;
use ViewComponents\Core\Block\ListBlock\Pagination\PaginationViewInterface;
use ViewComponents\Core\Common\MagicHubAccessTrait;

/**
 * Class Pagination
 *
 * @property int $pageSize
 * @property string $uriPageParam
 * @property-read PaginateOperation $operation
 * @property PaginationViewInterface $block
 * @property-read string $parentId
 */
class Pagination implements ComponentInterface
{
    Use MagicHubAccessTrait;

    const DEFAULT_URI_KEY = 'p';
    const SORT_POSITION = 9;

    /** @var HubInterface */
    protected $hub;

    public function __construct(
        $uriPageParam = self::DEFAULT_URI_KEY,
        $pageSize = 50,
        $parentId = Compound::CONTAINER_BLOCK
    )
    {
        $this->hub = new Hub([
            new Item('uriPageParam', $uriPageParam),
            new Item('pageSize', $pageSize),
            new Item('parentId', $parentId),
            new Item('operation', null),
            new Item('block', function () {
                return new PaginationTemplate();
            }),
            new Relation(
                'operation',
                ['currentPage', 'pageSize'],
                function (&$operation, $currentPage, $pageSize) {
                    $operation = new PaginateOperation($currentPage, $pageSize);
                }
            )
        ]);

    }

    public function register(HubInterface $hub)
    {
        $this->hub = new SubHub('pagination.', $this->hub, $hub);
        $this->hub->addDefinition(new Item('currentPage'));
        $hub->addDefinitions([
            new Relation(
                'pagination.currentPage',
                [InnerBlock::getFullId('form'), 'pagination.uriPageParam'],
                function (&$currentPage, Form $form, $uriPageParam) {
                    $currentPage = $form->getInputValue($uriPageParam, 1);
                }
            ),
            new Relation(
                'query',
                'pagination.operation',
                function (QueryInterface $query, PaginateOperation $operation = null, PaginateOperation $prev = null) {
                    if ($prev) {
                        $query->removeOperation($prev);
                    }
                    $query->addOperation($operation);
                }
            ),
            new Relation(
                'pagination.block',
                ['pagination.operation', 'pagination.uriPageParam', 'query'],
                function (PaginationViewInterface $block, PaginateOperation $operation = null, $uriPageParam, QueryInterface $query) {

                    // calculate total pages
                    $query->removeOperation($operation);
                    $totalPages = (int)ceil($query->count() / $operation->getPageSize());
                    $query->addOperation($operation);

                    $block
                        ->setUriKey($uriPageParam)
                        ->setCurrent($operation->getPageNumber())
                        ->setTotal($totalPages);

                    if ($block->getSortPosition() === null) {
                        $block->setSortPosition(self::SORT_POSITION);
                    }
                }
            ),
            new Relation($this->parentId, 'pagination.block', InnerBlock::attachInnerBlockFunc()),
        ]);
    }
}
