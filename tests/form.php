<link
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
    crossorigin="anonymous"
>
<?php

use ViewComponents\Core\Block\Form;
use ViewComponents\Core\Block\Tag;
use ViewComponents\Core\Customization\TwitterBootstrap;

require __DIR__ . '/../vendor/autoload.php';


$form = new Form([
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
$c = new Tag('div', ['class' => 'container']);
$c->addInnerBlock($form);
TwitterBootstrap::make()->apply($form);

echo $c->render();

\dump("test ended");