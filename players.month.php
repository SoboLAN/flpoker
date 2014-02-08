<?php

require_once 'autoload.php';

use FileListPoker\Content\PlayersMonthContent;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\PlayersMonthRenderer;
use FileListPoker\Main\FLPokerException;

try {
    $site = new Site();

    $playersMonthPage = new PlayersMonthContent();
    $players = $playersMonthPage->getContent();
    
    $renderer = new PlayersMonthRenderer($site);

    $pageContent = file_get_contents('templates/player.month.tpl');
    $pageContent = $renderer->render($pageContent, $players);
    
    $htmlout = $site->getFullPageTemplate('players.month.php');

} catch (FLPokerException $ex) {
    switch ($ex->getType()) {
        case FLPokerException::ERROR:
            header('Location: 500.shtml');
			exit();
            break;
        case FLPokerException::INVALID_REQUEST:
            header('Location: 400.shtml');
			exit();
            break;
        case FLPokerException::SITE_OFFLINE:
            header('Location: maintenance.shtml');
			exit();
            break;
        default:
            header('Location: 500.shtml');
			exit();
    }
}

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{bottom_page_scripts}'),
    array('content-narrower', $pageContent, ''),
    $htmlout
);
    
echo $htmlout;