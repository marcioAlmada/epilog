<?php

namespace Epilog\Interfaces;

interface LineParserInterface
{
    /**
     * @param string $log
     */
    public function parse($log);
}
