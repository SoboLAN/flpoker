<?php

require_once 'autoload.php';

use Exception as Exception;

use FileListPoker\Main\Database;
use FileListPoker\Main\Logger;
use FileListPoker\Main\Config;
use FileListPoker\Content\PlayerContent;

if (! Config::getValue('online')) {
    header('HTTP 500 Internal Server Error');
    exit();
}

try {
    $db = Database::getConnection();
    
    $content = new PlayerContent();
    
    $details = $content->getGeneral($_GET['id']);
    
    if (count($details) == 0) {
        $message = 'Non-existent player ID specified when acccessing get.player.details.php';
        Logger::log($message);
        header('HTTP 404 Not Found');
        exit();
    }
    
    echo json_encode($details);
    
} catch (Exception $e) {
    Logger::log('retrieving player details failed with $_GET = ' . print_r($_GET, true) . ': ' . $e->getMessage());
    header('HTTP 500 Internal Server Error');
    exit();
}
