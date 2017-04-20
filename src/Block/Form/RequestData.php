<?php

namespace ViewComponents\Core\Block\Form;

use Nayjest\DI\HubInterface;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Form;

class RequestData implements ComponentInterface
{
    /**
     * @var array
     */
    private $input;

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function register(HubInterface $hub)
    {
        $hub->builder()->defineRelation('requestData', null, function (&$input) {
            $input = array_merge($input ?: [], $this->input);
        });
    }
}
