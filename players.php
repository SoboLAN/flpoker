<?php

require_once 'autoload.php';

use FileListPoker\Pages\PlayersPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\PlayersRenderer;
use FileListPoker\Main\FLPokerException;

try {
    $site = new Site();
    
    $playersPage = new PlayersPage();
    $players = $playersPage->getContent();

    $renderer = new PlayersRenderer($site);
    
    $pageContent = file_get_contents('templates/players.tpl');
    $pageContent = $renderer->render($pageContent, $players);
    
    $htmlout = $site->getFullPageTemplate('players.php');

} catch (FLPokerException $ex) {
    switch ($ex->getType()) {
        case FLPokerException::ERROR:
            header('Location: 500.shtml');
            break;
        case FLPokerException::INVALID_REQUEST:
            header('Location: 400.shtml');
            break;
        case FLPokerException::SITE_DOWN:
            header('Location: maintenance.shtml');
            break;
        default:
            header('Location: 500.shtml');
    }
}

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{bottom_page_scripts}'),
    array('content-narrower', $pageContent, ''),
    $htmlout
);
    
echo $htmlout;