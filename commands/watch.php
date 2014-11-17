<?php namespace Epilog;

$handler->optionsFirst = false;
$response = $handler->handle(file_get_contents(__DIR__ . '/../spec/epilog_watch'));
$epilog = new Epilog($response);
$logFinder = LogFinderFactory::getLogFinder($response['--app']);
$log = new LogTail($logFinder->find($response['<file>']));
$epilog->run($log);
