<?php

require_once 'autoload.php';

use FileListPoker\Pages\PlayerPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\PlayerRenderer;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;


try {
    $site = new Site();

    $htmlout = $site->getHeader('players.php');

    $htmlout .= '<div id="title">' . $site->getWord('menu_players') . '</div>
                <div id="content-narrower">';

    if (! isset($_GET['id'])) {
        $message = 'No player ID specified when accessing player.php';
        Logger::log($message);
        throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
    }
    //eliminate some junk... (people can put all sorts of crap in this thing)...
    else if (strlen ($_GET['id']) > 4 ||
            ! is_numeric ($_GET['id']) ||
            strpos ($_GET['id'], '.') !== FALSE ||
            strpos ($_GET['id'], "'") !== FALSE)
    {
        $message = 'Invalid player ID specified when acccessing player.php';
        Logger::log($message);
        throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
    }

    $player_id = $_GET['id'];

    $playerPage = new PlayerPage();

    $general = $playerPage->getGeneral($player_id);

    if (count($general) == 0) {
        $message = 'Non-existent player ID specified when acccessing player.php';
        Logger::log($message);
        throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
    }

    $renderer = new PlayerRenderer($site);

    $general = $renderer->renderGeneral($general);
    $thistory = $renderer->rendererTHistory($playerPage->getTournamentHistory($player_id));
    $bonuses = $renderer->renderBonuses($playerPage->getBonuses($player_id));
    $prizes = $renderer->renderPrizes($playerPage->getPrizes($player_id));

    $htmlout .=
        '<div id="tabs">
            <ul>
                <li><a href="#tabs-1">' . $site->getWord('player_tab_general_title') . '</a></li>
                <li><a href="#tabs-2">' . $site->getWord('player_tab_thistory_title') . '</a></li>
                <li><a href="#tabs-3">' . $site->getWord('player_tab_bonuses_title') . '</a></li>
                <li><a href="#tabs-4">' . $site->getWord('player_tab_prizes_title') . '</a></li>
            </ul>
            <div id="tabs-1">' . $general . '</div>
            <div id="tabs-2">' . $thistory . '</div>
            <div id="tabs-3">' . $bonuses . '</div>
            <div id="tabs-4">' . $prizes . '</div>
        </div>';
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

$htmlout .=
    '<script>
        $(function() {
            $( "#tabs" ).tabs();
        });
    </script>';

$htmlout .= '</body></html>';
    
echo $htmlout;