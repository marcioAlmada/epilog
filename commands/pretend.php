<?php namespace Epilog;

$handler->optionsFirst = false;
$response = $handler->handle(file_get_contents(__DIR__ . '/../spec/epilog_pretend'));
$epilog = new Epilog($response);
$log = new FakeLogTail;
$epilog->run($log);
