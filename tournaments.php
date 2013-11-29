<?php

require_once 'autoload.php';

use FileListPoker\Pages\TournamentsPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\TournamentsRenderer;
use FileListPoker\Main\FLPokerException;

try {
    $site = new Site();

    $htmlout = $site->getHeader('tournaments.php');

    $htmlout .= '<div id="title">' . $site->getWord('menu_tournaments') . '</div>
                <div id="content-narrower">';

    $tournamentsPage = new TournamentsPage();
    $content = $tournamentsPage->getContent();
    
    $renderer = new TournamentsRenderer($site);

    $htmlout .= $renderer->render($content);
    
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

$htmlout .= '</div>';
    
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
    
echo $htmlout;