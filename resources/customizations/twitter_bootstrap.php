<?php

use ViewComponents\Core\Block\Form\AbstractInput;
use ViewComponents\Core\Block\Form\Input;
use ViewComponents\Core\Block\Form\Select;
use ViewComponents\Core\Block\ListBlock;
use ViewComponents\Core\Block\ListBlock\Pagination\PaginationTemplate;
use ViewComponents\Core\Block\Tag;
use ViewComponents\Core\Customization\TwitterBootstrap;

return [
    'options' => [
        // adds input-<size> to inputs and buttons
        'control-size' => TwitterBootstrap::CONTROL_SIZE_SMALL,
        'uri' => [
            'css' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
            'js' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
        ],

    ],
    AbstractInput::class => function (AbstractInput $block) {
        $block->containerBlock
            ->addClass('form-group');
    },
    Input::class => function (Input $block, array $options) {
        $block->inputBlock->addClass('form-control');
        if (!empty($options['control-size'])) {
            $block->inputBlock->addClass('input-' . $options['control-size']);
        }
    },
    Select::class => function (Select $block, array $options) {
        $block->selectBlock->addClass('form-control');
        if (!empty($options['control-size'])) {
            $block->selectBlock->addClass('input-' . $options['control-size']);
        }
    },
    ListBlock::class => function (ListBlock $block) {
        $block->formBlock->formBlock
            ->addClass('form-inline');
    },
    Tag::class => function (Tag $tag, array $options) {
        $name = $tag->getName();
        // BUTTON
        if ($name === 'button') {
            $tag->addClass('btn');
            if (!empty($options['control-size'])) {
                $tag->addClass('btn-' . $options['control-size']);
            }
            if ($tag->getAttribute('type') === 'reset') {
                $tag
                    ->addClass('btn-warning')
                    ->setBlockSeparator(' ')
                    ->addInnerBlock(
                        Tag::make('i')
                            ->addClasses(['glyphicon', 'glyphicon-erase'])
                            ->setSortPosition(-1)
                    );
            } else {
                $tag->addClass('btn-default');
            }
        }
        // SUBMIT
        if ($tag->getAttribute('type') === 'submit') {
            $tag->addClasses([
                'btn',
                'btn-primary',
            ]);
            if (!empty($options['control-size'])) {
                $tag->addClass('btn-' . $options['control-size']);
            }
            if ($name === 'button') {
                $tag
                    ->setBlockSeparator(' ')
                    ->addInnerBlock(
                    Tag::make('i')
                        ->addClasses(['glyphicon', 'glyphicon-refresh'])
                        ->setSortPosition(-1)
                );
            }
        }
        // TABLES
        if ($name === 'table') {
            $tag->addClasses([
                'table',
                'table-bordered'
            ]);
        }
    },
    PaginationTemplate::class => function (PaginationTemplate $block) {
        $prefix = "customizations/twitter_bootstrap/";
        $mainTemplate = 'list_block/pagination';
        $linkTemplate = 'list_block/pagination/link';
        if ($block->getTemplateName() === $mainTemplate) {
            $block->setTemplateName($prefix . $mainTemplate);
        }
        if ($block->getLinkTemplateName() === $linkTemplate) {
            $block->setLinkTemplateName($prefix . $linkTemplate);
        }
    },
];
