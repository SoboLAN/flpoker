<?php

require_once 'autoload.php';

use FileListPoker\Pages\RankingsPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\RankingsRenderer;

$site = new Site();

$htmlout = $site->getHeader('rankings.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_rankings') . '</div>';

$htmlout .= '<div id="content-narrower">';

$rankingsPage = new RankingsPage();

$renderer = new RankingsRenderer($site);

$topAllTime = $renderer->renderTopAllTime($rankingsPage->getTopPlayersAllTime());
$mostActive = $renderer->renderMostActivePlayers($rankingsPage->getMostActive50Players());
$top6Months = $renderer->render6Months($rankingsPage->getTop40Players6Months());
$finalTables = $renderer->renderFinalTables($rankingsPage->getTop50FinalTables());

$htmlout .=
	'<div id="tabs">
		<ul>
			<li><a href="#tabs-1">' . $site->getWord('statistics_tab_top_all_time') . '</a></li>
			<li><a href="#tabs-2">' . $site->getWord('statistics_tab_top_6_months') . '</a></li>
			<li><a href="#tabs-3">' . $site->getWord('statistics_tab_most_active') . '</a></li>
			<li><a href="#tabs-4">' . $site->getWord('statistics_tab_final_tables') . '</a></li>
		</ul>
		<div id="tabs-1">
			<p>' . $site->getWord('statistics_top_all_time_text') . '</p>
			' . $topAllTime . '
		</div>
		<div id="tabs-2">
			<p>' . $site->getWord('statistics_top_6_months_text') . '</p>
			' . $top6Months . '
		</div>
		<div id="tabs-3">
			<p>' . $site->getWord('statistics_most_active_text') . '</p>
			' . $mostActive . '
		</div>
		<div id="tabs-4">
			<p>' . $site->getWord('statistics_final_tables_text') . '</p>
			' . $finalTables . '
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