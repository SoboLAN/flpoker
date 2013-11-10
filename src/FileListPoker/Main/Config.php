<?php

namespace FileListPoker\Main;

/**
 * This class contains configuration options for the site.
 */
class Config
{
    private static $configPath = 'src/FileListPoker/Main/main.config.json';
    
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
            //fill the array with key => value pairs. you can easily deduce the purpose
            //of each one by the key names
            self::$siteConfig = json_decode(file_get_contents(self::$configPath), true);
        }
        
        return isset(self::$siteConfig[$key]) ? self::$siteConfig[$key] : null;
    }
}
