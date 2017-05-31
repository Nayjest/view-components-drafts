<?php


use ViewComponents\Core\Block\Block;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\DefinitionBuilder;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Tag;

require __DIR__ . '/../vendor/autoload.php';

$c = new Compound([
    new InnerBlock('h1', null, new Tag('h1')),
    new InnerBlock('caption', 'h1', new Block('text')),
    new InnerBlock('footer', null, new Tag('div')),
    new InnerBlock('footerText', 'footer', new Block('footer')),
    DefinitionBuilder::make()
        ->define('caption', 'Default Caption')
        ->usedByBlock('caption', function(Block $block, $val) {
            $block->setData($val);
        })
]);

$t = new InnerBlock('t', 'h1', new Block('[t]'));
$c->addComponent($t);
echo $c->render();

// Reuse
$c->caption = "New caption2";
$t->setParentId('footer');
echo $c->render();

$t->setBlock(new Block('[t2]'));

echo $c->render();