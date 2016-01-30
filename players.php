<?php

require_once 'vendor/autoload.php';

use FileListPoker\Content\PlayersContent;
use FileListPoker\Main\Site;
use FileListPoker\Main\Config;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Renderers\Paginator;
use FileListPoker\Renderers\PlayersRenderer;
use FileListPoker\Renderers\PaginationRenderer;
use FileListPoker\Renderers\FullPageRenderer;

use Symfony\Component\HttpFoundation\Response;

$site = new Site();

//validate page
$errors = $site->isValidNumericQueryParameter('page', 2, 1);
if (count($errors) > 0) {
    $message = 'Invalid page specified when acccessing players.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
}

//build parameters
$page = $site->request->query->get('page', 1);
$perPage = Config::getValue('players_pagination_page_size');
$paginationWidth = Config::getValue('players_pagination_width');

//get templates
$playersTpl = file_get_contents('templates/players.tpl');
$paginationBlockTpl = file_get_contents('templates/pagination/block.tpl');
$paginationElementTpl = file_get_contents('templates/pagination/element.tpl');

//get the players
$playersPage = new PlayersContent();
$players = $playersPage->getPlayers($page, $perPage);

if (count($players) == 0) {
    $message = 'Non-existent page specified when acccessing players.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
}

$totalPlayers = $playersPage->getPlayersCount();

//get pagination
$paginator = new Paginator($totalPlayers, $perPage, $page, $paginationWidth);
$pagination = $paginator->getPagination();

//build renderers
$playersRenderer = new PlayersRenderer($site);
$paginationRenderer = new PaginationRenderer($site);

//render players
$renderedPlayers = $playersRenderer->render($playersTpl, $players, $page, $perPage);

//render pagination
$renderedPagination = $paginationRenderer->render(
    $paginationBlockTpl,
    $paginationElementTpl,
    $pagination,
    'players.php'
);

$mainRenderer = new FullPageRenderer($site);
$htmlout = $mainRenderer->renderPage('players.php');

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{pagination}', '{bottom_page_scripts}'),
    array('content-narrower', $renderedPlayers, $renderedPagination, ''),
    $htmlout
);

$site->response->setContent($htmlout);
$site->response->setStatusCode(Response::HTTP_OK);
$site->response->send();
