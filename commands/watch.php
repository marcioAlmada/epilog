<?php namespace Epilog;

$handler->optionsFirst = false;
$response = $handler->handle(file_get_contents(__DIR__ . '/../spec/epilog_watch'));
$epilog = new Epilog($response);
$log = new LogTail($response['<file>']);
$epilog->run($log);
