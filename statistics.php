<?php

require_once 'Site.class.php';
require_once 'StatisticsRenderer.php';
require_once 'StatisticsPage.php';

$site = new Site();

$htmlout = $site->getHeader('statistics.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_statistics') . '</div>';

$htmlout .= '<div id="content">';

$statisticsPage = new StatisticsPage();

$renderer = new StatisticsRenderer($site);

$tournaments = $renderer->renderTournamentGraph($statisticsPage->getTournamentsGraph());
$topAllTime = $renderer->renderTopAllTime($statisticsPage->getTopPlayersAllTime());
$mostActive = $renderer->renderMostActivePlayers($statisticsPage->getMostActive50Players());
$general = $renderer->renderGeneral($statisticsPage->getGeneralStatistics());
$top6Months = $renderer->render6Months($statisticsPage->getTop40Players6Months());

$htmlout .=
	'<div id="tabs">
		<ul>
			<li><a href="#tabs-1">' . $site->getWord('statistics_tab_general') . '</a></li>
			<li><a href="#tabs-2">' . $site->getWord('statistics_tab_top_all_time') . '</a></li>
			<li><a href="#tabs-3">' . $site->getWord('statistics_tab_top_6_months') . '</a></li>
			<li><a href="#tabs-4">' . $site->getWord('statistics_tab_most_active') . '</a></li>
			<li><a href="#tabs-5">' . $site->getWord('statistics_tab_tournaments') . '</a></li>
		</ul>
		<div id="tabs-1">
			<p>
			<span class="bigger_label">' . $site->getWord('statistics_general_totalpoints') . ': ' .
				$general .
			'</span>
			</p>
		</div>
		<div id="tabs-2">
			<p>' . $site->getWord('statistics_top_all_time_text') . '</p>
			' . $topAllTime . '
		</div>
		<div id="tabs-3">
			<p>' . $site->getWord('statistics_top_6_months_text') . '</p>
			' . $top6Months . '
		</div>
		<div id="tabs-4">
			<p>' . $site->getWord('statistics_most_active_text') . '</p>
			' . $mostActive . '
		</div>
		<div id="tabs-5">
			<p>' . $site->getWord('statistics_tournaments_text') . '</p>
			' . $tournaments . '
			<div id="hcc" style="width:90%; height: 450px; margin: 0 auto;"></div>
		</div>
	</div>';

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .=
	'<script>
		$(function() {
			$( "#tabs" ).tabs();
		});
	</script>';

$htmlout .= '</body></html>';
	
echo $htmlout;