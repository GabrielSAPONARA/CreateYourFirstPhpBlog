<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

return function($name = 'app')
{
    $logger = new Logger($name);

    $logFile = __DIR__ . "/../logs/{$name}.log";
    $logDir = dirname($logFile);

    if(!is_dir($logDir))
    {
        mkdir($logDir, 0775, true);
    }

    $logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

    return $logger;
};