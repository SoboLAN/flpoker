<?php

require_once 'vendor/autoload.php';

use FileListPoker\Content\PlayersMonthContent;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\PlayersMonthRenderer;
use FileListPoker\Renderers\FullPageRenderer;

use Symfony\Component\HttpFoundation\Response;

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

$site->response->setContent($htmlout);
$site->response->setStatusCode(Response::HTTP_OK);
$site->response->send();
