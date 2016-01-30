<?php

require_once 'vendor/autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Cache\CacheFactory;

$site = new Site();

$cache = CacheFactory::getCacheInstance();

$cache->flushAll();
