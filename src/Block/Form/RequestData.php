<?php

namespace ViewComponents\Core\Block\Form;

use ViewComponents\Core\Block\Compound;
use ViewComponents\Core\Block\Compound\Component\ComponentInterface;
use ViewComponents\Core\Block\Compound\Component\Event;
use ViewComponents\Core\Block\Form;

class RequestData implements ComponentInterface
{
    const EVENT_ID = 'set_request_data';
    /**
     * @var array
     */
    private $input;

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function getId()
    {
        return 'form_request_data';
    }

    public function handle($eventId, Compound $root)
    {
        if ($eventId === Compound::EVENT_SET_ROOT) {
            if (!$root instanceof Form) {
                throw new \Exception("Invalid root, form expected.");
            }
            $root->addComponent(
                Event::make(self::EVENT_ID)
                    ->after(Compound::EVENT_ATTACH_INNER_BLOCKS)
                    ->before(Form::EVENT_UPDATE_VALUES)

            );
        } elseif ($eventId === self::EVENT_ID) {
            /** @var Form $root */
            $root->setInputData($this->input);
        }
    }
}