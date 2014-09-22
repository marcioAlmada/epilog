<?php

// we can be on a phar file, a project composer dependency or a global composer package
// so ne need to find the autoload file
foreach (['/vendor/autoload.php', '/../autoload.php', '/../../autoload.php'] as $file) {
    if (file_exists(__DIR__ . $file)) {
        include(__DIR__ . $file);
        break;
    }
}

// global error handler
set_exception_handler(function ($e) {
    echo "\e[0m", $e->getMessage(), "\n";
    exit($e->getCode());
});
