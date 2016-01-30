<?php

require_once 'vendor/autoload.php';

use FileListPoker\Content\StatusContent;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\StatusRenderer;
use FileListPoker\Renderers\FullPageRenderer;

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

$mainRenderer = new FullPageRenderer($site);
$htmlout = $mainRenderer->renderPage('status.php');

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