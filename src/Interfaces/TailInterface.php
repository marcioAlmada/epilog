<?php

namespace Epilog\Interfaces;

interface TailInterface
{
    public function fgets();
    public function eof();
    public function getRealPath();
}
