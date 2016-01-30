<?php

namespace FileListPoker\Main\Cache;

use FileListPoker\Main\Cache\PDOCache;
use FileListPoker\Main\Config;
use FileListPoker\Main\Database;

use Doctrine\Common\Cache\CacheProvider;

class CacheFactory
{
    /**
     * @var CacheProvider
     */
    private static $cache;
    
    /**
     * Returns an instance of CacheProvider based on site configuration.
     * NULL is returned if caching is disabled or can not be instantiated.
     * @return null|CacheProvider
     */
    public static function getCacheInstance()
    {
        if (is_null(self::$cache) && Config::getValue('enable_cache')) {
            $cacheType = Config::getValue('cache_type');

            if ($cacheType === 'pdo') {
                $db = Database::getConnection();
                self::$cache = new PDOCache($db);
                self::$cache->setNamespace(Config::getValue('cache_ns'));
            }
        }
        
        return self::$cache;
    }
}
