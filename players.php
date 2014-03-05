<?php

require_once 'autoload.php';

use FileListPoker\Content\PlayersContent;
use FileListPoker\Renderers\Paginator;
use FileListPoker\Main\Site;
use FileListPoker\Main\Config;
use FileListPoker\Renderers\PlayersRenderer;
use FileListPoker\Renderers\PaginationRenderer;
use FileListPoker\Main\FLPokerException;

try {
    $site = new Site();
    
    //validate page
    if (! isset($_GET['page'])) {
        $page = 1;
    } elseif (! $site->isValidID($_GET['page'])) {
        $message = 'Invalid page specified when acccessing players.php';
        Logger::log($message);
        throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
    }

    //build parameters
    $page = $_GET['page'];
    $perPage = Config::getValue('players_pagination_page_size');
    $paginationWidth = Config::getValue('players_pagination_width');
    
    //get templates
    $playersTpl = file_get_contents('templates/players.tpl');
    $paginationBlockTpl = file_get_contents('templates/pagination/block.tpl');
    $paginationElementTpl = file_get_contents('templates/pagination/element.tpl');
    
    //get the players
    $playersPage = new PlayersContent();
    $players = $playersPage->getContent($page, $perPage);
    
    //get pagination
    $paginator = new Paginator(count($players), $perPage, $page, $paginationWidth);
    $pagination = $paginator->getPagination();
    
    //build renderers
    $playersRenderer = new PlayersRenderer($site);
    $paginationRenderer = new PaginationRenderer($site);
    
    //render players
    $playersContent = $renderer->render(playersTpl, $players);
    
    //render pagination
    $paginationContent = $paginationRenderer->render($blockTpl, $elementTpl, $pagination);
    
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
    array('content-narrower', $playersContent, $paginationContent, ''),
    $htmlout
);
    
echo $htmlout;