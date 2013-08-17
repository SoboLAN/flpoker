<?php

require_once 'GeneralRenderer.php';
require_once 'Site.class.php';

class StatisticsRenderer extends GeneralRenderer
{
	private $site;
	
	public function __construct(Site $site)
	{
		$this->site = $site;
	}
	
	public function renderGeneral($content)
	{
		$out = '
		<p>
			<span class="bigger_label">' . $this->site->getWord('statistics_general_totalpoints') . ': ' .
				number_format($content['total_points']) .
			'</span>
		</p>
		<p>
			<span class="bigger_label">' . $this->site->getWord('statistics_general_nrusers') . ': ' .
				$content['total_players'] .
			'</span>
		</p>
		<p>
			<span class="bigger_label">' . $this->site->getWord('statistics_general_nrtournaments') . ': ' .
				$content['total_tournaments'] .
			'</span>
		</p>';
		
		return $out;
	}
	
	public function renderMostActivePlayers($content)
	{
		$out = '<table class="presentation-table" style="width:70%; margin: 0 auto">
			<tr>
			<th><strong>Nr.</strong></th>
			<th><strong>' . $this->site->getWord('statistics_mostactive_player') . '</strong></th>
			<th><strong>' . $this->site->getWord('statistics_mostactive_count') . '</strong></th>
			</tr>';

		$i = 1;
		foreach ($content as $player)
		{		
			$out .=
			'<tr>
				<td>' . $i . '</td>
				<td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
				<td>' . $player['count'] . '</a></td>
			</tr>';

			$i++;
		}

		$out .= '</table>';

		return $out;
	}
	
	public function renderTournamentGraph($content)
	{
		$tournamentParticipants = array();
		$tournamentAverage = array();
		foreach ($content as $tournament)
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
					text: '" . $this->site->getWord('statistics_tournaments_charttitle') . "'
				},
				subtitle: {
					text: '" . $this->site->getWord('statistics_tournaments_chartsubtitle') . "'
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
						text: '" . $this->site->getWord('statistics_tournaments_playersline') . "'
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
					name: '" . $this->site->getWord('statistics_tournaments_playersline') . "',
					// Note that in JavaScript, months start at 0 for January, 1 for February etc.
					data: [
						$tournamentParticipants
					]
				}, {
					name: '" . $this->site->getWord('statistics_tournaments_averageline') . "',
					data: [
						$tournamentAverage
					]
				}]
			});
		});
		</script>";

		return $out;
	}
	
	public function renderTopAllTime($content)
	{
		$out = '<table class="presentation-table" style="width:90%; margin: 0 auto">
			<tr>
			<th><strong>Nr</strong></th>
			<th><strong>' . $this->site->getWord('players_pokerstars_name') . '</strong></th>
			<th><strong>' . $this->site->getWord('players_filelist_name') . '</strong></th>
			<th><strong>' . $this->site->getWord('players_points_all_time') . '</strong></th>
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
	
	public function render6Months($content)
	{
		/*
		$out = '<table class="presentation-table" style="width:70%; margin: 0 auto">
				<tr>
				<th><strong>Nr.</strong></th>
				<th><strong>' . $site->getWord('statistics_6months_player') . '</strong></th>
				<th><strong>' . $site->getWord('statistics_6months_points') . '</strong></th>
				</tr>';

		$i = 1;
		foreach ($players as $player)
		{		
			$out .=
			'<tr>
				<td>' . $i . '</td>
				<td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
				<td>' . $player['points'] . '</a></td>
			</tr>';

			$i++;
		}

		$out .= '</table>';*/

		$out = 'This feature is currently disabled.';

		return $out;
	}
}