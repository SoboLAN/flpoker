<?php

require_once 'autoload.php';

use FileListPoker\Content\StatisticsContent;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\StatisticsRenderer;
use FileListPoker\Main\FLPokerException;

try {
    $site = new Site();
    
    $statisticsPage = new StatisticsContent();

    $generalContent = $statisticsPage->getGeneralStatistics();
    $tournamentsContent = $statisticsPage->getTournamentsGraph();
    $registrationsContent = $statisticsPage->getRegistrationsGraph();
    $aggressionContent = $statisticsPage->getAggressionGraph();
    
    $pageContent = file_get_contents('templates/statistics/statistics.tpl');
    
    $pageContent = str_replace(
        array(
            '{statistics_tab_general}',
            '{statistics_tab_tournaments}',
            '{statistics_tab_registrations}',
            '{statistics_tab_aggresion}'
        ),
        array(
            $site->getWord('statistics_tab_general'),
            $site->getWord('statistics_tab_tournaments'),
            $site->getWord('statistics_tab_registrations'),
            $site->getWord('statistics_tab_aggresion')
        ),
        $pageContent
    );
    
    $generalTpl = file_get_contents('templates/statistics/general.tpl');
    $tournamentsTpl = file_get_contents('templates/statistics/tournament.graph.tpl');
    $registrationsTpl = file_get_contents('templates/statistics/registrations.graph.tpl');
    $aggresionTpl = file_get_contents('templates/statistics/aggression.graph.tpl');
    
    $renderer = new StatisticsRenderer($site);
    
    $general = $renderer->renderGeneral($generalTpl, $generalContent);
    $tournaments = $renderer->renderTournamentGraph($tournamentsTpl, $tournamentsContent);
    $registrations = $renderer->renderRegistrationsGraph($registrationsTpl, $registrationsContent);
    $aggressionFactor = $renderer->renderAggressionGraph($aggresionTpl, $aggressionContent);
    
    $pageContent = str_replace(
        array('{generalStatistics}', '{tournamentsGraph}', '{registrationsGraph}', '{aggressionFactorGraph}'),
        array($general, $tournaments, $registrations, $aggressionFactor),
        $pageContent
    );

    $htmlout = $site->getFullPageTemplate('statistics.php');
    
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
    array('content', $pageContent, $bottomScript),
    $htmlout
);

echo $htmlout;