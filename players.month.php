<?php

require_once 'autoload.php';

use FileListPoker\Content\PlayersMonthContent;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\PlayersMonthRenderer;
use FileListPoker\Renderers\FullPageRenderer;

$site = new Site();

$playersMonthPage = new PlayersMonthContent();
$players = $playersMonthPage->getPlayersOfTheMonth();

$renderer = new PlayersMonthRenderer($site);

$pageContent = file_get_contents('templates/player.month.tpl');
$pageContent = $renderer->render($pageContent, $players);

$mainRenderer = new FullPageRenderer($site);
$htmlout = $mainRenderer->renderPage('players.month.php');

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{bottom_page_scripts}'),
    array('content-narrower', $pageContent, ''),
    $htmlout
);
    
echo $htmlout;