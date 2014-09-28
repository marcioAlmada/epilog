<?php

namespace Epilog\Interfaces;

interface LineParserInterface
{
    /**
     * @param string $log raw log entry
     */
    public function parse($log);
}
