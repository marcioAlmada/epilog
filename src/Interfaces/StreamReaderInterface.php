<?php

namespace Epilog\Interfaces;

interface StreamReaderInterface
{
    public function __construct($wrapper);
    public function readChar();
    public function readLine();
}
