<?php

require_once 'autoload.php';

use FileListPoker\Pages\StatisticsPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\StatisticsRenderer;

$site = new Site();

$htmlout = $site->getHeader('statistics.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_statistics') . '</div>';

$htmlout .= '<div id="content">';

$statisticsPage = new StatisticsPage();

$renderer = new StatisticsRenderer($site);

$tournaments = $renderer->renderTournamentGraph($statisticsPage->getTournamentsGraph());
$registrations = $renderer->renderRegistrationsGraph($statisticsPage->getRegistrationsGraph());
$general = $renderer->renderGeneral($statisticsPage->getGeneralStatistics());

$htmlout .=
    '<div id="tabs">
        <ul>
            <li><a href="#tabs-1">' . $site->getWord('statistics_tab_general') . '</a></li>
            <li><a href="#tabs-2">' . $site->getWord('statistics_tab_tournaments') . '</a></li>
            <li><a href="#tabs-3">' . $site->getWord('statistics_tab_registrations') . '</a></li>
        </ul>
        <div id="tabs-1">
            ' . $general . '
        </div>
        <div id="tabs-2">
            <p>' . $site->getWord('statistics_tournaments_text') . '</p>
            ' . $tournaments . '
            <div id="hcc" style="width:90%; height: 450px; margin: 0 auto;"></div>
        </div>
        <div id="tabs-3">
            <p>' . $site->getWord('statistics_registrations_text') . '</p>
            ' . $registrations . '
            <div id="highc-reg" style="width:90%; height: 450px; margin: 0 auto;"></div>
        </div>
    </div>';

$htmlout .= '</div>';
    
$htmlout .= $site->getFooter();

$htmlout .=
    '<script>
        $(function() {
            $("#tabs").tabs();
        });
    </script>';

$htmlout .= '</body></html>';
    
echo $htmlout;