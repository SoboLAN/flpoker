<?php

require_once 'vendor/autoload.php';

use FileListPoker\Content\TournamentsContent;
use FileListPoker\Renderers\Paginator;
use FileListPoker\Main\Site;
use FileListPoker\Main\Config;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Renderers\TournamentsRenderer;
use FileListPoker\Renderers\PaginationRenderer;
use FileListPoker\Renderers\FullPageRenderer;

$site = new Site();

//validate page
if (isset($_GET['page']) && ! $site->isValidID($_GET['page'])) {
    $message = 'Invalid page specified when acccessing tournaments.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
}

//build parameters
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$perPage = Config::getValue('tournaments_pagination_page_size');
$paginationWidth = Config::getValue('tournaments_pagination_width');

//get templates
$tournamentsTpl = file_get_contents('templates/tournaments.tpl');
$paginationBlockTpl = file_get_contents('templates/pagination/block.tpl');
$paginationElementTpl = file_get_contents('templates/pagination/element.tpl');

//get the tournaments
$tournamentsContent = new TournamentsContent();
$tournaments = $tournamentsContent->getTournaments($page, $perPage);

if (count($tournaments) == 0) {
    $message = 'Non-existent page specified when acccessing tournaments.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
}

$totalTournaments = $tournamentsContent->getTournamentCount();

//get pagination
$paginator = new Paginator($totalTournaments, $perPage, $page, $paginationWidth);
$pagination = $paginator->getPagination();

//build renderers
$tournamentsRenderer = new TournamentsRenderer($site);
$paginationRenderer = new PaginationRenderer($site);

//render tournaments
$renderedTournaments = $tournamentsRenderer->render($tournamentsTpl, $tournaments);

//render pagination
$renderedPagination = $paginationRenderer->render(
    $paginationBlockTpl,
    $paginationElementTpl,
    $pagination,
    'tournaments.php'
);

$mainRenderer = new FullPageRenderer($site);
$htmlout = $mainRenderer->renderPage('tournaments.php');

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{pagination}', '{bottom_page_scripts}'),
    array('content-narrower', $renderedTournaments, $renderedPagination, ''),
    $htmlout
);
    
echo $htmlout;