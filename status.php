<?php

require_once 'autoload.php';

use FileListPoker\Content\StatusContent;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\StatusRenderer;
use FileListPoker\Main\FLPokerException;

try {
    $site = new Site();

    $statusPage = new StatusContent();
    
    $standingsContent = $statusPage->getCurrentStandings();
    $fTablesContent = $statusPage->getFinalTables();
    
    $pageContent = file_get_contents('templates/status/status.tpl');

    $pageContent = str_replace(
        array(
            '{status_tab_standings_title}',
            '{status_tab_ftables_title}'
        ),
        array(
            $site->getWord('status_tab_standings_title'),
            $site->getWord('status_tab_ftables_title')
        ),
        $pageContent
    );
    
    $standingsTpl = file_get_contents('templates/status/standings.tpl');
    $fTablesTpl = file_get_contents('templates/status/final.tables.tpl');
    
    $renderer = new StatusRenderer($site);
    
    $standings = $renderer->renderCurrentStandings($standingsTpl, $standingsContent);
    $finalTables = $renderer->rendererFinalTables($fTablesTpl, $fTablesContent);
    
    $pageContent = str_replace(
        array('{status_tab_standings_content}', '{status_tab_ftables_content}'),
        array($standings, $finalTables),
        $pageContent
    );
    
    $htmlout = $site->getFullPageTemplate('status.php');
    
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

$bottomScript =
    '<script>
        $(function() {
            $("#tabs").tabs();
        });
    </script>';

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{bottom_page_scripts}'),
    array('content-narrower', $pageContent, $bottomScript),
    $htmlout
);

echo $htmlout;