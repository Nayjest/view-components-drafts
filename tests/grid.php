<?php
use Nayjest\Querying\ArrayQuery;
use Nayjest\Querying\Operation\AddFieldOperation;
use Nayjest\Querying\Operation\FilterOperation;
use Nayjest\Querying\Row\ArrayRow;
use Nayjest\Querying\Row\RowInterface;
use ViewComponents\Core\Block\Compound\Component\SubComponent;
use ViewComponents\Core\Block\Form\ResetButton;
use ViewComponents\Core\Block\Grid;
use ViewComponents\Core\Block\Grid\ColumnSortingControl;
use ViewComponents\Core\Block\ListBlock\Filter;
use ViewComponents\Core\Block\ListBlock;
use ViewComponents\Core\Block\ListBlock\PageSizeSelect;
use ViewComponents\Core\Block\ListBlock\Pagination;
use ViewComponents\Core\Customization\TwitterBootstrap;
use ViewComponents\Core\Block\Grid\Column;
use ViewComponents\Core\Services;


require __DIR__ . '/../vendor/autoload.php';


$data = [
    ['name' => 'Robert', 'age' => 32],
    ['name' => 'Jack', 'age' => 24],
    ['name' => 'Anna', 'age' => 24],
    ['name' => 'User1', 'age' => 45],
    ['name' => 'User2', 'age' => 21],
    ['name' => 'User3', 'age' => 97],
    ['name' => 'User4', 'age' => 15],
    ['name' => 'User5', 'age' => 11],
    ['name' => 'User6', 'age' => 23],
    ['name' => 'User7', 'age' => 63],
    ['name' => 'User8', 'age' => 83],
    ['name' => 'User9', 'age' => 26],
    ['name' => 'User10', 'age' => 15],
    ['name' => 'User11', 'age' => 45],
];
$query = new ArrayQuery($data, ArrayRow::class);
$query->addOperation(
    new AddFieldOperation('year_of_birth', function(RowInterface $row) {
        return date('Y') - $row->get('age');
    })
);
$pagination = new Pagination('page', 5);
$pagination->block = new Pagination\PaginationTemplate();
$grid = new Grid($query, [
    new Column('name', 'Name'),
    new Column('age', 'Age'),
    new Column('year_of_birth', 'Year of Birth'),
    new ColumnSortingControl('name'),
    new ColumnSortingControl('age'),
    new ColumnSortingControl('year_of_birth'),
    new Filter('name', FilterOperation::OPERATOR_STR_CONTAINS),
    new Filter('age', FilterOperation::OPERATOR_EQ),
    $pagination,
   // new SortingControl(['name', 'age']),
    new PageSizeSelect([2, 5, 10, 20], 5),
    new SubComponent(ListBlock::FORM_BLOCK, new ResetButton()),
]);
//$grid->paginationBlock = new Pagination\PaginationTemplate();
TwitterBootstrap::make()->apply($grid);


//TwitterBootstrap::make()->apply($grid);
echo $grid->render();
echo Services::resourceManager()->render();
