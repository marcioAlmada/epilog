<?php

namespace Epilog\Interfaces;

interface InputReaderInterface
{
    public function __construct($wrapper);

    /**
     * @return \Epilog\InputReader
     */
    public function block($block = true);

    /**
     * @return string
     */
    public function readChar();

    /**
     * @return string
     */
    public function readLine();
}
