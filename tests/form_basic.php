<?php
use ViewComponents\Core\Block\Form;

require __DIR__ . '/../vendor/autoload.php';

$loginValidator = new Form\Validator('login', function ($value) {
    if (strlen($value) < 3) {
        return ['Minimal login length: 3'];
    }
    return true;
});

$form = new Form([
    $input = new Form\Input('login', 'Login'),
    $loginValidator,
    new Form\Input('email', 'E-mail'),
    new Form\Input('birthDate', 'BD', '2001-01-01'),
    new Form\Select('someSelect', 'Some Select', [
        '' => '--//--',
        'a' => 'A',
        'b' => 'B'
    ]),
    new Form\RequestData($_GET)
]);
$input->addErrors(['Initial error example']);
//$c = new \ViewComponents\Core\Block\Compound([$form]);
?>
<h3>Form:</h3>
<?= $form->render() ?>
<h3>Form.RequestData</h3>
<?php \dump($form->requestData) ?>
<h3>Form.InputData</h3>
<?php \dump($form->inputData) ?>
