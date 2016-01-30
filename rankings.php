<?php

require_once 'vendor/autoload.php';

use FileListPoker\Content\RankingsContent;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\RankingsRenderer;
use FileListPoker\Renderers\FullPageRenderer;

use Symfony\Component\HttpFoundation\Response;

$site = new Site();

$rankingsPage = new RankingsContent();

$topAllTimeContent = $rankingsPage->getTopPlayersAllTime();
$topMostActiveContent = $rankingsPage->getMostActive50Players();
$topSixMonthsContent = $rankingsPage->getTop40Players6Months();
$topFinalTablesContent = $rankingsPage->getTop50FinalTables();

$pageContent = file_get_contents('templates/rankings/rankings.tpl');

$pageContent = str_replace(
    array(
        '{statistics_tab_top_all_time}',
        '{statistics_tab_top_6_months}',
        '{statistics_tab_most_active}',
        '{statistics_tab_final_tables}'
    ),
    array(
        $site->getWord('statistics_tab_top_all_time'),
        $site->getWord('statistics_tab_top_6_months'),
        $site->getWord('statistics_tab_most_active'),
        $site->getWord('statistics_tab_final_tables')
    ),
    $pageContent
);

$allTimeTpl = file_get_contents('templates/rankings/top.all.time.tpl');
$sixMonthsTpl = file_get_contents('templates/rankings/top.six.months.tpl');
$mostActiveTpl = file_get_contents('templates/rankings/top.most.active.tpl');
$finalTablesTpl = file_get_contents('templates/rankings/top.final.tables.tpl');

$renderer = new RankingsRenderer($site);

$topAllTime = $renderer->renderTopAllTime($allTimeTpl, $topAllTimeContent);
$topSixMonths = $renderer->render6Months($sixMonthsTpl, $topSixMonthsContent);
$topMostActive = $renderer->renderMostActivePlayers($mostActiveTpl, $topMostActiveContent);
$topFinalTables = $renderer->renderFinalTables($finalTablesTpl, $topFinalTablesContent);

$pageContent = str_replace(
    array('{rankings_tab_alltime}', '{rankings_tab_6months}', '{rankings_tab_mostactive}', '{rankings_tab_ftables}'),
    array($topAllTime, $topSixMonths, $topMostActive, $topFinalTables),
    $pageContent
);

$mainRenderer = new FullPageRenderer($site);
$htmlout = $mainRenderer->renderPage('rankings.php');

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
    
$site->response->setContent($htmlout);
$site->response->setStatusCode(Response::HTTP_OK);
$site->response->send();
