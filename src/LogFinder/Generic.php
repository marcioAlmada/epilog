<?php

namespace Epilog\LogFinder;

use Epilog\FlowException;

class Generic
{
    public function find($file)
    {
        if(! is_readable($file) || is_dir($file))
            throw new FlowException("Could not read log file '{$file}'");

        return $file;
    }
}
