<?php
use ViewComponents\Core\Event\EventSequence;

require __DIR__ . '/../vendor/autoload.php';

$e = new EventSequence();
$e->define('a')->before('e');
$e->define('b');
$e->define('c');
$e->define('d');
$e->define('e')->after('a');
$e->define('f');
$e->define('a.1')->before('e')->after('a');
$e->define('c.1')->after('c');
$e->define('c.2')->before('e')->after('c.1');
var_dump(join(',', $e->getEvents()));