<?php

require_once 'autoload.php';

use FileListPoker\Main\Config;
use FileListPoker\Main\Site;
use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Content\PlayerContent;

//since this page is called via AJAX, this check is kept in order to maintain correct behaviour
if (! Config::getValue('online')) {
    header('HTTP/1.1 503 Service Unavailable');
    exit();
}

$site = new Site();

$db = Database::getConnection();

$content = new PlayerContent();

$details = $content->getGeneral($_GET['id']);

if (count($details) == 0) {
    $message = 'Non-existent player ID specified when acccessing get.player.details.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
}

echo json_encode($details);
