<?php


use ViewComponents\Core\Block\Form\Input;

require __DIR__ .'/../vendor/autoload.php';
$name = new Input('name', 'Label!!', 77);
$name->setErrors([
    'error text 1',
    'error text 2'
]);
$name = serialize($name);
$name = unserialize($name);
//$name->containerBlock->setName('span');

?>
<form>
    <?= $name->render() ?>
    <?= (new Input('id', 'Id', 77))->render(); ?>
</form>
