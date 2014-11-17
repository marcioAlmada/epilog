<?php

namespace Epilog\Interfaces;

interface TailInterface
{
    public function fgets();

    /**
     * @return boolean
     */
    public function eof();

    /**
     * @return string
     */
    public function getRealPath();

    /**
     * @return void
     */
    public function seekLastLineRead();
}
