<?php

require_once 'autoload.php';
use FileListPoker\Main\Database;
use FileListPoker\Main\Logger;

try {
    $db = Database::getConnection();
    
    //delete expired cache entries
    $result = $db->query ('DELETE FROM cache WHERE entry_time + lifetime < UNIX_TIMESTAMP(NOW())');
} catch (\PDOException $e) {
    Logger::log('cache cleanup failed: ' . $e->getMessage());
}