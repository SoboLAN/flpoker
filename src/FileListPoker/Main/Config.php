<?php

namespace FileListPoker\Main;

use FileListPoker\Main\FLPokerException;

/**
 * This class contains configuration options for the site.
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class Config
{
    //location of config file
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
        //the configuration options must only be read once (save speed by reducing IO operations)
        if (is_null(self::$siteConfig)) {
            
            if (! is_readable(self::$configPath)) {
                $ex = new FLPokerException('config file is inaccessible', FLPokerException::ERROR);
                throw $ex;
            }
            
            self::$siteConfig = json_decode(file_get_contents(self::$configPath), true);
            
            //json_decode returns NULL if provided string cannot be decoded
            //this almost always means a corrupt file
            if (is_null(self::$siteConfig)) {
                $ex = new FLPokerException('config file is corrupt', FLPokerException::ERROR);
                throw $ex;
            }
        }
        
        //return option or null if invalid key was provided
        return array_key_exists($key, self::$siteConfig) ? self::$siteConfig[$key] : null;
    }
}
