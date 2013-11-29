<?php

require_once 'autoload.php';

use FileListPoker\Pages\TournamentPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\TournamentRenderer;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;

try {
    $site = new Site();

    $htmlout = $site->getHeader('tournaments.php');

    $htmlout .= '<div id="title">' . $site->getWord('menu_tournaments') . '</div>
                <div id="content-narrower">';

    if (! isset($_GET['id'])) {
        $message = 'No tournament ID specified when accessing tournament.php';
        Logger::log($message);
        throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
    }
    //eliminate some junk... (people can put all sorts of stuff in this thing)...
    else if (strlen ($_GET['id']) > 4 ||
            ! is_numeric ($_GET['id']) ||
            strpos ($_GET['id'], '.') !== FALSE ||
            strpos ($_GET['id'], "'") !== FALSE)
    {
        $message = 'Invalid tournament ID specified when acccessing tournament.php';
        Logger::log($message);
        throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
    }

    $tid = $_GET['id'];

    $tournamentPage = new TournamentPage();
    $details = $tournamentPage->getTournamentDetails($tid);
    $results = $tournamentPage->getTournamentResults($tid);
    $bonuses = $tournamentPage->getTournamentBonuses($tid);

    if (! isset($details['id'])) {
        $message = 'Non-existent tournament ID specified when acccessing tournament.php';
        Logger::log($message);
        throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
    }
    
    $renderer = new TournamentRenderer($site);

    $htmlout .= $renderer->renderDetails($details);

    $htmlout .= $renderer->renderResults($results);

    $htmlout .= $renderer->renderBonuses($bonuses);

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