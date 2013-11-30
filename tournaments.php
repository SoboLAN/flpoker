<?php

require_once 'autoload.php';

use FileListPoker\Pages\TournamentsPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\TournamentsRenderer;
use FileListPoker\Main\FLPokerException;

try {
    $site = new Site();
    
    $tournamentsPage = new TournamentsPage();
    $tournaments = $tournamentsPage->getContent();
    
    $renderer = new TournamentsRenderer($site);
    
    $pageContent = file_get_contents('templates/tournaments.tpl');
    $pageContent = $renderer->render($pageContent, $tournaments);

    $htmlout = $site->getFullPageTemplate('tournaments.php');
    
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