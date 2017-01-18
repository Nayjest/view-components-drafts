<?php

use ViewComponents\Core\Block\SimpleListView;
use ViewComponents\Core\Block\VarDump;

require __DIR__ .'/../vendor/autoload.php';

$data = [
    ['name' => 'Robert', 'age' => 32],
    ['name' => 'Jack', 'age' => 24],
];
$list = new SimpleListView(new VarDump(), $data);