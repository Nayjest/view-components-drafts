<?php

use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Form;

require __DIR__ . '/../vendor/autoload.php';


$form = new \ViewComponents\Core\Block\Form([
    new Form\Input('login', 'Login'),
    new Form\Input('email', 'E-mail'),
    new Form\Input('birthDate', 'BD', '1970-01-01'),
    new Form\Select('someSelect', 'Some Select', [
        '' => '--//--',
        'a' => 'A',
        'b' => 'B'
    ]),
    new Form\RequestData($_GET)
]);


//$form->setInputData($_GET);
// test serialization
//$s = serialize($form);
//$form = unserialize($s);

// test validator
$birthDateValidator = new Form\Validator('birthDate', function ($value) {
    $d = DateTime::createFromFormat('Y-m-d', $value);
    return $d && $d->format('Y-m-d') === $value;
});
$form->addComponent($birthDateValidator);


// test reusage
//$form->render();
//$form->render();

$loginValidator = new Form\Validator('login', function ($value) {
    if (strlen($value) < 3) {
        return ['Minimal login length: 3'];
    }
    return true;
});


$form->addComponent($loginValidator);
echo $form->render();

\dump("test ended");