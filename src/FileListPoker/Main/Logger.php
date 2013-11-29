<?php

namespace FileListPoker\Main;

use FileListPoker\Main\Config;

class Logger
{
    private static $errorFile = 'log/flpoker.errors.log';
    
    public static function log($message)
    {
        if (Config::getValue('enable_logging') and is_writable(self::$errorFile)) {
            file_put_contents(
                self::$errorFile,
                date('[Y-m-d H:i:s]', time()) . " $message\n",
                FILE_APPEND | LOCK_EX
            );
        }
    }
}
