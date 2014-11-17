<?php

namespace Epilog\Interfaces;

interface InputReaderInterface
{
    /**
     * @return void
     */
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
