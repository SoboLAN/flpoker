<?php

namespace FileListPoker\Main;

use FileListPoker\Main\Config;

/**
 * Class that handles logging functionality.
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class Logger
{
    //path to the log file
    private static $errorFile = 'log/flpoker.errors.log';
    
    /**
     * The specified message will be logged by this function. Keep in mind that this function
     * won't do anything if logging is disabled or if the log file is inaccessible
     * @param string $message the message you want to log
     */
    public static function log($message)
    {
        if (Config::getValue('enable_logging') && is_writable(self::$errorFile)) {
            file_put_contents(
                self::$errorFile,
                date('[Y-m-d H:i:s]', time()) . " $message\n",
                FILE_APPEND | LOCK_EX
            );
        }
    }
}
