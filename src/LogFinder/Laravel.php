<?php

namespace Epilog\LogFinder;

use Epilog\FlowException;

class Laravel
{
    public function find($dir)
    {
        if(! is_readable($dir))
            throw new FlowException("Could not find log file at '{$file}'");

        if(is_file($dir)) { // don't try to find log if we already have a log file
            $log = $dir;
        } else {
            if($dir === '.') $dir = getcwd();
            $dir = preg_replace('#/$#', '', $dir);
            $logs =
                glob($dir . "/app/storage/logs/*.txt") // laravel 4.0.*
                +
                glob($dir . "/app/storage/logs/*.log") // laravel 4.2.*
                ; 
            $logs = array_combine($logs, array_map("filemtime", $logs));
            arsort($logs);
            $log = key($logs);
        }

        return $log; // return last modified log file
    }
}
