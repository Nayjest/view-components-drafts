<?php

namespace ViewComponents\core\Input;

/**
 * InputSource is a factory class for InputOption instances.
 */
class InputSource
{
    /**
     * @var array
     */
    private $input;

    /**
     * Constructor.
     *
     * @param array $input $_GET, $_POST, etc. can be used as input
     */
    public function __construct(array $input)
    {
        $this->input = $input;
    }

    /**
     * Creates input option.
     *
     * @param string $name
     * @param null $default optional default value
     * @return InputParameter
     */
    final public function parameter($name, $default = null)
    {
        return new InputParameter($name, $this->input, $default);
    }

    /**
     * Shortcut for InputSource::parameter().
     *
     * @param string $name
     * @param null $default optional default value
     * @return InputParameter
     */
    public function __invoke($name, $default = null)
    {
        return $this->parameter($name, $default);
    }
}
