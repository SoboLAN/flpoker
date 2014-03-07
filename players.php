<?php

require_once 'autoload.php';

use FileListPoker\Content\PlayersContent;
use FileListPoker\Renderers\Paginator;
use FileListPoker\Main\Site;
use FileListPoker\Main\Config;
use FileListPoker\Main\Logger;
use FileListPoker\Renderers\PlayersRenderer;
use FileListPoker\Renderers\PaginationRenderer;
use FileListPoker\Main\FLPokerException;

try {
    $site = new Site();
    
    //validate page
    if (isset($_GET['page']) && ! $site->isValidID($_GET['page'])) {
        $message = 'Invalid page specified when acccessing players.php';
        Logger::log($message);
        throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
    }

    //build parameters
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
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
        Logger::log($message);
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
    
    $htmlout = $site->getFullPageTemplate('players.php');

} catch (FLPokerException $ex) {
    switch ($ex->getType()) {
        case FLPokerException::ERROR:
            header('Location: 500.shtml');
			exit();
            break;
        case FLPokerException::INVALID_REQUEST:
            header('Location: 400.shtml');
			exit();
            break;
        case FLPokerException::SITE_OFFLINE:
            header('Location: maintenance.shtml');
			exit();
            break;
        default:
            header('Location: 500.shtml');
			exit();
    }
}

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{pagination}', '{bottom_page_scripts}'),
    array('content-narrower', $renderedPlayers, $renderedPagination, ''),
    $htmlout
);
    
echo $htmlout;