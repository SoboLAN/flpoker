<?php

require_once 'autoload.php';

use FileListPoker\Pages\PlayerPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\PlayerRenderer;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;


try {
    $site = new Site();

    if (! isset($_GET['id'])) {
        $message = 'No player ID specified when accessing player.php';
        Logger::log($message);
        throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
    } elseif (! $site->isValidID($_GET['id'])) {
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
    
    $tournamentHistory = $playerPage->getTournamentHistory($player_id);
    $bonuses = $playerPage->getBonuses($player_id);
    $prizes = $playerPage->getPrizes($player_id);
    
    $pageContent = file_get_contents('templates/player/player.tpl');
    $renderer = new PlayerRenderer($site);
    
    $pageContent = str_replace(
        '{player_tab_general_title}',
        $site->getWord('player_tab_general_title'),
        $pageContent
    );
    $pageContent = str_replace(
        '{player_tab_thistory_title}',
        $site->getWord('player_tab_thistory_title'),
        $pageContent
    );
    $pageContent = str_replace(
        '{player_tab_bonuses_title}',
        $site->getWord('player_tab_bonuses_title'),
        $pageContent
    );
    $pageContent = str_replace(
        '{player_tab_prizes_title}',
        $site->getWord('player_tab_prizes_title'),
        $pageContent
    );
    
    $pageContent = $renderer->renderGeneral($pageContent, $general);
    $pageContent = $renderer->rendererTHistory($pageContent, $tournamentHistory);
    $pageContent = $renderer->renderBonuses($pageContent, $bonuses);
    $pageContent = $renderer->renderPrizes($pageContent, $prizes);
    
    $htmlout = $site->getFullPageTemplate('player.php');
    
    $htmlout = str_replace('{content_type_id}', 'content-narrower', $htmlout);
    $htmlout = str_replace('{page_content}', $pageContent, $htmlout);

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

$bottomScript = '<script>
        $(function() {
            $( "#tabs" ).tabs();
        });
    </script>';

$htmlout = str_replace('{bottom_page_scripts}', $bottomScript, $htmlout);

echo $htmlout;