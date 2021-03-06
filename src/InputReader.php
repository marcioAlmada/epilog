<?php

namespace Epilog;

use Epilog\Interfaces\InputReaderInterface;

class InputReader implements InputReaderInterface
{
    protected $resource;

    public function __construct($wrapper = 'php://stdin')
    {
        $this->resource = fopen($wrapper, 'r');
    }

    public function block($block = true)
    {
        stream_set_blocking($this->resource, (int) $block);

        return $this;
    }

    public function readChar()
    {
        return fgetc($this->resource);
    }

    public function readLine()
    {
        $input = fgets($this->resource);

        return (false !== $input) ? trim(chop($input)) : $input;
    }
}
