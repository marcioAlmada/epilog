<?php

namespace Epilog\Interfaces;

interface InputReaderInterface
{
    public function __construct($wrapper);
    public function block($block = true);
    public function readChar();
    public function readLine();
}
