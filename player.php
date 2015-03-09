<?php

require_once 'autoload.php';

use FileListPoker\Content\PlayerContent;
use FileListPoker\Main\Site;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Renderers\PlayerRenderer;
use FileListPoker\Renderers\FullPageRenderer;

$site = new Site();

if (! isset($_GET['id'])) {
    $message = 'No player ID specified when accessing player.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
} elseif (! $site->isValidID($_GET['id'])) {
    $message = 'Invalid player ID specified when acccessing player.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
}

$player_id = $_GET['id'];

$playerPage = new PlayerContent();

$general = $playerPage->getGeneral($player_id);

if (count($general) == 0) {
    $message = 'Non-existent player ID specified when acccessing player.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
}

$tournamentHistory = $playerPage->getTournamentHistory($player_id);
$bonuses = $playerPage->getBonuses($player_id);
$prizes = $playerPage->getPrizes($player_id);

$pageContent = file_get_contents('templates/player/player.tpl');
$renderer = new PlayerRenderer($site);

$pageContent = str_replace(
    array(
        '{player_tab_general_title}',
        '{player_tab_thistory_title}',
        '{player_tab_bonuses_title}',
        '{player_tab_prizes_title}',
    ),
    array(
        $site->getWord('player_tab_general_title'),
        $site->getWord('player_tab_thistory_title'),
        $site->getWord('player_tab_bonuses_title'),
        $site->getWord('player_tab_prizes_title'),
    ),
    $pageContent
);

$tHistoryTemplate = file_get_contents('templates/player/player_tournament_history.tpl');
$bonusesTemplate = file_get_contents('templates/player/player_bonuses.tpl');
$prizesTemplate = file_get_contents('templates/player/player_prizes.tpl');

$pageContent = $renderer->renderGeneral($pageContent, $general);
$tHistoryTemplate = $renderer->rendererTHistory($tHistoryTemplate, $tournamentHistory);
$bonusesTemplate = $renderer->renderBonuses($bonusesTemplate, $bonuses);
$prizesTemplate = $renderer->renderPrizes($prizesTemplate, $prizes);

$pageContent = str_replace(
    array('{player_tab_tournament_history}', '{player_tab_bonuses}', '{player_tab_prizes}'),
    array($tHistoryTemplate, $bonusesTemplate, $prizesTemplate),
    $pageContent
);

$mainRenderer = new FullPageRenderer($site);

$htmlout = $mainRenderer->renderPage('players.php');

$bottomScript = '<script>
        $(function() {
            $( "#tabs" ).tabs();
        });
    </script>';

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{bottom_page_scripts}'),
    array('content-narrower', $pageContent, $bottomScript),
    $htmlout
);

echo $htmlout;