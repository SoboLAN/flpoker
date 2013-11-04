<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;

class StatisticsRenderer extends GeneralRenderer
{
	private $site;
	
	public function __construct(Site $site)
	{
		$this->site = $site;
	}
	
	public function renderGeneral($content)
	{
		$percentage = number_format((100.0 * $content['total_spent']) / $content['total_points'], 2);
		
		$out = '
		<p>
			<span class="bigger_label">' . $this->site->getWord('statistics_general_totalpoints') . ': ' .
				number_format($content['total_points']) .
			'</span>
		</p>
		<p>
			<span class="bigger_label">' . $this->site->getWord('statistics_general_spent') . ': ' .
				number_format($content['total_spent']) .
				' (' . $percentage . ' % ' . $this->site->getWord('statistics_general_outoftotal') . ')' .
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
	
	public function renderRegistrationsGraph($content)
	{
		$clubRegistrants = array();
		foreach ($content as $record)
		{
			$clubRegistrants[] =
			"[Date.UTC({$record['join_year']}, {$record['join_month']} - 1, 1), {$record['nr_players']}]";
		}

		$clubRegistrants = implode(",\n", $clubRegistrants);

		$out = "<script type=\"text/javascript\">
		$(function () {
			$('#highc-reg').highcharts({
				chart: {
					type: 'spline'
				},
				title: {
					text: '" . $this->site->getWord('statistics_registrations_charttitle') . "'
				},
				subtitle: {
					text: '" . $this->site->getWord('statistics_registrations_chartsubtitle') . "'
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
						text: '" . $this->site->getWord('statistics_registrations_nrplayersline') . "'
					},
					min: 0
				},
				tooltip: {
					formatter: function() {
							return '<b>'+ this.series.name +'</b><br/>'+
							Highcharts.dateFormat('%b %Y', this.x) +': '+ this.y;
					}
				},
				series: [{
					name: '" . $this->site->getWord('statistics_registrations_nrplayersline') . "',
					// Note that in JavaScript, months start at 0 for January, 1 for February etc.
					data: [
						$clubRegistrants
					]
				}]
			});
		});
		</script>";

		return $out;
	}
}