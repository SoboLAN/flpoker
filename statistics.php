<?php

require_once 'Site.class.php';

require_once 'DAO/StatisticsPage.php';

$site = new Site();

function getTournamentGraph($tournaments)
{
	global $site;

	$tournamentParticipants = array();
	$tournamentAverage = array();
	foreach ($tournaments as $tournament)
	{
		$tournamentParticipants[] =
		"[Date.UTC({$tournament['year']}, {$tournament['month']} - 1, {$tournament['day']}), {$tournament['participants']}]";

		$tournamentAverage[] =
		"[Date.UTC({$tournament['year']}, {$tournament['month']} - 1, {$tournament['day']}), {$tournament['average_participants']}]";
	}

	$tournamentParticipants = implode(",\n", $tournamentParticipants);
	$tournamentAverage = implode(",\n", $tournamentAverage);

	$out = "<script type=\"text/javascript\">
	$(function () {
        $('#hcc').highcharts({
            chart: {
                type: 'spline'
            },
            title: {
                text: '" . $site->getWord('statistics_tournaments_charttitle') . "'
            },
            subtitle: {
                text: '" . $site->getWord('statistics_tournaments_chartsubtitle') . "'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: { // don't display the dummy year
                    month: '%e. %b',
                    year: '%b'
                }
            },
            yAxis: {
                title: {
                    text: '" . $site->getWord('statistics_tournaments_playersline') . "'
                },
                min: 0
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        Highcharts.dateFormat('%e %b %Y', this.x) +': '+ this.y;
                }
            },
            
            series: [{
                name: '" . $site->getWord('statistics_tournaments_playersline') . "',
                // Note that in JavaScript, months start at 0 for January, 1 for February etc.
                data: [
                    $tournamentParticipants
                ]
            }, {
                name: '" . $site->getWord('statistics_tournaments_averageline') . "',
                data: [
                    $tournamentAverage
                ]
            }]
        });
    });
	</script>";
	
	return $out;
}

function getPlayersAllTime($content)
{
	global $site;

	$out = '<table class="presentation-table" style="width:90%">
			<tr>
			<th><strong>Nr</strong></th>
			<th><strong>' . $site->getWord('players_pokerstars_name') . '</strong></th>
			<th><strong>' . $site->getWord('players_filelist_name') . '</strong></th>
			<th><strong>' . $site->getWord('players_points_all_time') . '</strong></th>
			</tr>';

	$i = 1;
	foreach ($content as $player)
	{
		$out .=
		'<tr>
			<td>' . $i . '</td>
			<td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
			<td><a href="http://filelist.ro/userdetails.php?id=' . $player['id_filelist'] . '">' . $player['name_filelist'] . '</a></td>
			<td>' . $player['points'] . '</td>
		</tr>';
		
		$i++;
	}

	$out .= '</table>';
	
	return $out;
}

$htmlout = $site->getHeader('statistics.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_statistics') . '</div>';

$htmlout .= '<div id="content">';

$statisticsPage = new StatisticsPage();
$tournaments = $statisticsPage->getTournamentsGraph();
$topAllTime = $statisticsPage->getTopPlayersAllTime();

$htmlout .=
	'<div id="tabs">
		<ul>
			<li><a href="#tabs-1">General Statistics</a></li>
			<li><a href="#tabs-2">' . $site->getWord('statistics_tab_top_all_time') . '</a></li>
			<li><a href="#tabs-3">' . $site->getWord('statistics_tab_top_6_months') . '</a></li>
			<li><a href="#tabs-4">' . $site->getWord('statistics_tab_most_active') . '</a></li>
			<li><a href="#tabs-5">' . $site->getWord('statistics_tab_tournaments') . '</a></li>
		</ul>
		<div id="tabs-1">
			<p>General Statistics: </p>
			<p>' . $site->getWord('statistics_top_bonus_text') . '</p>
			<p>' . $site->getWord('statistics_top_spending_text') . '</p>
		</div>
		<div id="tabs-2">
			<p>' . $site->getWord('statistics_top_all_time_text') . '</p>
			' . getPlayersAllTime($topAllTime) . '
		</div>
		<div id="tabs-3">
			<p>' . $site->getWord('statistics_top_6_months_text') . '</p>
		</div>
		<div id="tabs-4">
			<p>' . $site->getWord('statistics_most_active_text') . '</p>
		</div>
		<div id="tabs-5">
			<p>' . $site->getWord('statistics_tournaments_text') . '</p>
			' . getTournamentGraph($tournaments) . '
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