<link
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
    crossorigin="anonymous"
>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<!--<script
    src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
    integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
    crossorigin="anonymous"
></script>-->

<?php
use Nayjest\Querying\ArrayQuery;
use Nayjest\Querying\Operation\FilterOperation;
use Nayjest\Querying\Row\ArrayRow;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;

use ViewComponents\Core\Block\Compound\Component\SubComponent;
use ViewComponents\Core\Block\Form\ResetButton;
use ViewComponents\Core\Block\ListBlock\Filter;
use ViewComponents\Core\Block\ListBlock;
use ViewComponents\Core\Block\ListBlock\PageSizeSelect;
use ViewComponents\Core\Block\ListBlock\Pagination;
use ViewComponents\Core\Block\ListBlock\SortingControl;
use ViewComponents\Core\Customization\TwitterBootstrap;


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

$list = new ListBlock(
    new ArrayQuery($data, ArrayRow::class),
    [
        new Filter('name', FilterOperation::OPERATOR_STR_CONTAINS),
        new Filter('age', FilterOperation::OPERATOR_EQ),
        new Pagination('page', 5),
        new SortingControl(['name', 'age']),
        new PageSizeSelect([2, 5, 10, 20], 5),
        new SubComponent(ListBlock::FORM_BLOCK, new ResetButton())
//        new RecordView(new DataPresenter(
//            function($data, Tag $tag) {
//                $tag->getInnerBlocks()[0]->getInnerBlocks()[0]->setData($data);
//            },
//            Tag::make('div', [], [
//                Tag::make('pre', [], [
//                    new Json(null, JSON_PRETTY_PRINT)
//                ])
//            ])
//        ))
    ]
);

TwitterBootstrap::make()->apply($list);
echo $list->render();
//    new ArrayQuery($data, ArrayRow::class),
//$list = new ListBlock(
//    new ArrayQuery($data, ArrayRow::class),
//    null,
//    //new Json(null, JSON_PRETTY_PRINT),
//    [
//        new Filter('age', FilterOperation::OPERATOR_GTE),
//        /*
//        new Validator('age_gte', function ($val) {
//            if (!$val) return true;
//            return is_numeric($val);
//        }),
//        */
//        new Filter('age', FilterOperation::OPERATOR_LTE),
//        new Filter(
//            'age',
//            FilterOperation::OPERATOR_EQ,
//            new Select(
//                'age1',
//                'Age',
//                [
//                    '' => '--//--',
//                    24 => 24,
//                    25 => 25
//                ]
//            )
//        ),
//        Filter::makeWithSelect('name', [
//            '' => '--//--',
//            'Robert' => 'Robert',
//            'Jack' => 'Jack'
//        ]),
//
//        new SortingControl([
//            '' => 'None',
//            'age' => 'Age',
//            'name' => 'Name'
//        ]),
//        new Pagination('p', 3),
//        new PageSizeSelect(
//            new Select('records_per_page', 'Records per page!!', [
//                '' => 'default value',
//                5 => 5,
//                10 => 10
//            ])
//        )
//
//
//    ]
//);
//
//$list->addComponent(new InnerBlock('collection.pre', new Tag('pre')));
//$list->getComponent('record_view')->moveTo('pre');
//
////$list->setBlock('record_view', new Json(null, JSON_PRETTY_PRINT));
//echo $list->render();