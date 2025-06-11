<?php

namespace App\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerManager
{
    private static array $loggers = [];
    private static $config;

    /**
     * @param string $name
     * @return Logger
     */
    public static function getLogger(string $name): Logger
    {
        if (!isset(self::$loggers[$name]))
        {
            if (!self::$config)
            {
                self::$config = require_once __DIR__ .
                                             '/../../config/monolog.php';
            }
            self::$loggers[$name] = (self::$config)($name);
        }

        return self::$loggers[$name];
    }
}