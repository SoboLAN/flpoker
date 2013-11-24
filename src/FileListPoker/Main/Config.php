<?php

namespace FileListPoker\Main;

use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;

/**
 * This class contains configuration options for the site.
 */
class Config
{
    private static $configPath = 'config/main.config.json';
    
    //array containing configuration options
    private static $siteConfig;
    
    /**
     * Returns the value of the option specified by the key. The list of possible keys
     * should be in the documentation.
     * @param string $key the name of the required option.
     * @return mixed the value of the requested option or null if it doesn't exist.
     */
    public static function getValue($key)
    {
        if (is_null(self::$siteConfig)) {
            
            if (! is_readable(self::$configPath)) {
                $ex = new FLPokerException('config file is inaccessible', FLPokerException::ERROR);
                Logger::log($ex->getMessage());
                throw $ex;
            }
            
            self::$siteConfig = json_decode(file_get_contents(self::$configPath), true);
            
            if (is_null(self::$siteConfig)) {
                $ex = new FLPokerException('config file is corrupt', FLPokerException::ERROR);
                Logger::log($ex->getMessage());
                throw $ex;
            }
        }
        
        return array_key_exists($key, self::$siteConfig) ? self::$siteConfig[$key] : null;
    }
}
