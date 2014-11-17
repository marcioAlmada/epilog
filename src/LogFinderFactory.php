<?php

namespace Epilog;

class LogFinderFactory
{
    public static function getLogFinder($framework = 'generic'){
        $finder = '\Epilog\LogFinder\\' . ucfirst($framework);
        if(! class_exists($finder))
            throw new FlowException("Log finder for {$framework} is not available");

        return new $finder;
    }
}
