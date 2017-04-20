<?php


use ViewComponents\Core\Block\Block;
use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\DefinitionBuilder;
use ViewComponents\Core\Block\Compound\Component\InnerBlock;
use ViewComponents\Core\Block\Tag;

require __DIR__ . '/../vendor/autoload.php';

$c = new Compound([
    new InnerBlock('h1', new Tag('h1')),
    new InnerBlock('h1.caption', new Block('text')),
    new InnerBlock('footer', new Block('footer')),
    DefinitionBuilder::make()
        ->define('caption', 'Default Caption')
        ->usedByBlock('caption', function(Block $block, $val) {
            $block->setData($val);
        })
]);

echo $c->render();

// Reuse
$c->caption = "New caption2";
echo $c->render();
