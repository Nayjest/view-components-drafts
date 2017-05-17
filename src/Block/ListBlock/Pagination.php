<?php

namespace ViewComponents\Core\Block\ListBlock;

use Nayjest\DI\Definition\Item;
use Nayjest\DI\Definition\Relation;
use Nayjest\DI\HubInterface;
use Nayjest\DI\SubHub;
use Nayjest\Querying\Operation\PaginateOperation;
use Nayjest\Querying\QueryInterface;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Compound\Component\InnerBlock\InnerBlockRelation;
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
class Pagination extends Compound\Component\AbstractComponent
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
        parent::__construct([
            'uriPageParam' => $uriPageParam,
            'pageSize' => $pageSize,
            'parentId' => $parentId,
            new Item('operation', ['currentPage', 'pageSize'], function (PaginateOperation &$operation = null, $currentPage, $pageSize) {
                $operation = new PaginateOperation($currentPage, $pageSize);
            }),
            'block' => function () {
                return new PaginationTemplate();
            },
            new Item(
                'currentPage',
                [
                    SubHub::externalItemId(ListBlock::FORM_BLOCK),
                    'uriPageParam'
                ],
                function (&$currentPage, Form $form, $uriPageParam) {
                    $currentPage = $form->getInputValue($uriPageParam, 1);
                }
            ),
            new Relation(
                SubHub::externalItemId(ListBlock::QUERY),
                'operation',
                function (QueryInterface $query, PaginateOperation $operation = null, PaginateOperation $prev = null) {
                    if ($prev) {
                        $query->removeOperation($prev);
                    }
                    $query->addOperation($operation);
                }
            ),
        ]);
    }

    protected function getId()
    {
        return 'pagination';
    }

    public function register(HubInterface $hub)
    {
        parent::register($hub);
        //$this->hub->addDefinition(new Item('currentPage'));
        $hub->addDefinitions([
//            new Relation(
//                $this->externalId('currentPage'),
//                [ListBlock::FORM_BLOCK, $this->externalId('uriPageParam')],
//                function (&$currentPage, Form $form, $uriPageParam) {
//                    $currentPage = $form->getInputValue($uriPageParam, 1);
//                }
//            ),

            new Relation(
                $this->externalId('block'),
                [
                    $this->externalId('operation'),
                    $this->externalId('uriPageParam'),
                    ListBlock::QUERY
                ],
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
            new Relation(
                $this->parentId,
                $this->externalId('block'), InnerBlockRelation::getHandler()),
        ]);
    }
}
