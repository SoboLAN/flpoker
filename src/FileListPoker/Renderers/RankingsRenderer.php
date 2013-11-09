<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;

class RankingsRenderer extends GeneralRenderer
{
	private $site;
	
	public function __construct(Site $site)
	{
		$this->site = $site;
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
				<td>' . $player['count'] . '</td>
			</tr>';

			$i++;
		}

		$out .= '</table>';

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
			$namePokerStars = (is_null($player['name_pokerstars']) OR empty($player['name_pokerstars'])) ?
							'<span class="faded">unknown</span>' :
							$player['name_pokerstars'];
			
			if (is_null($player['name_filelist']) OR empty($player['name_filelist']))
			{
				$nameFilelist = '<span class="faded">unknown</span>';
				$flURL = $nameFilelist;
			}
			else
			{
				$nameFilelist = $player['name_filelist'];
				$flURL = '<a href="http://filelist.ro/userdetails.php?id=' . $player['id_filelist'] . '">' .
						$nameFilelist . '</a>';
			}
			
			$out .=
			'<tr>
				<td>' . $i . '</td>
				<td><a href="player.php?id=' . $player['player_id'] . '">' . $namePokerStars . '</a></td>
				<td>' . $flURL . '</td>
				<td>' . $player['points'] . '</td>
			</tr>';

			$i++;
		}

		$out .= '</table>';

		return $out;
	}
	
	public function render6Months($content)
	{
		$out = '<table class="presentation-table" style="width:70%; margin: 0 auto">
			<tr>
			<th><strong>Nr</strong></th>
			<th><strong>' . $this->site->getWord('statistics_6months_player') . '</strong></th>
			<th><strong>' . $this->site->getWord('statistics_6months_points') . '</strong></th>
			</tr>';

		$i = 1;
		foreach ($content as $player)
		{
			$out .=
			'<tr>
				<td>' . $i . '</td>
				<td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
				<td>' . $player['totalp'] . '</td>
			</tr>';

			$i++;
		}

		$out .= '</table>';

		return $out;
	}
	
	public function renderFinalTables($content)
	{
		$out = '<table class="presentation-table" style="width:70%; margin: 0 auto">
			<tr>
			<th><strong>Nr</strong></th>
			<th><strong>' . $this->site->getWord('statistics_final_tables_player') . '</strong></th>
			<th><strong>' . $this->site->getWord('statistics_final_tables_tables') . '</strong></th>
			</tr>';

		$i = 1;
		foreach ($content as $player)
		{
			$out .=
			'<tr>
				<td>' . $i . '</td>
				<td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
				<td>' . $player['final_tables'] . '</td>
			</tr>';

			$i++;
		}

		$out .= '</table>';

		return $out;
	}
}