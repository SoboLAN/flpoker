<?php

require_once 'GeneralRenderer.php';
require_once 'Site.class.php';

class TournamentRenderer extends GeneralRenderer
{
	private $site;
	
	public function __construct(Site $site)
	{
		$this->site = $site;
	}
	
	public function renderDetails($content)
	{
		if (empty ($content))
		{
			return '';
		}
		
		$tournamentTime = mktime(0, 0, 0, $content['month'], $content['day'], $content['year']);
		$tournamentDate = date('l, j F Y', $tournamentTime);
		
		if ($this->site->getLanguage() !== 'en')
		{
			$tournamentDate = $this->translateDate($tournamentDate, $this->site->getLanguage());
			$tournamentDate = $this->translateDay($tournamentDate, $this->site->getLanguage());
		}
		
		$type = $content['type'] == 'regular' ? $this->site->getWord('tournament_regular') : 
												$this->site->getWord('tournament_special');

		$out =
			'<p>
				<span class="subtitle">' . $this->site->getWord('tournament_details') . '</span>
			</p>
			<p>
				<span class="bigger_label">' . $this->site->getWord('tournament_date') . ': ' .
					$tournamentDate . '</span>
			</p>
			<p>
				<span class="bigger_label">' . $this->site->getWord('tournament_type') . ': ' .
					$type . '</span>
			</p>
			<p>
				<span class="bigger_label">' . $this->site->getWord('tournament_nrplayers') . ': ' .
					$content['participants'] . '</span>
			</p>';
		
		return $out;
	}
	
	public function renderResults($content)
	{
		if (empty ($content))
		{
			return '';
		}
		
		$out = '<p>
				<span class="subtitle">' . $this->site->getWord('tournament_results') . '</span>
			</p>';
		
		$out .= '<table class="presentation-table" style="width:100%">
			<tr>
			<th><strong>' . $this->site->getWord('tournament_position') . '</strong></th>
			<th><strong>' . $this->site->getWord('tournament_player') . '</strong></th>
			<th><strong>' . $this->site->getWord('tournament_points') . '</strong></th>
			</tr>';

		foreach ($content as $result)
		{
			if (isset ($result['player_id']) AND isset ($result['name_pokerstars']))
			{
				$player = '<a href="player.php?id=' . $result['player_id'] . '">' . $result['name_pokerstars'] . '</a>';
			}
			else
			{
				$player = '<span class="faded">unknown</span>';
			}

			$out .=
			'<tr>
				<td>' . $result['position'] . '</td>
				<td>' . $player . '</td>
				<td>' . $result['points'] . '</td>
			</tr>';	
		}

		$out .= '</table>';
		
		return $out;
	}
	
	public function renderBonuses ($content)
	{
		if (empty ($content))
		{
			return '';
		}
		
		$out = '<p>
				<span class="subtitle">' . $this->site->getWord('tournament_bonuses') . '</span>
			</p>';
		
		$out .= '<table class="presentation-table" style="width:100%">
			<tr>
			<th><strong>' . $this->site->getWord('tournament_player') . '</strong></th>
			<th><strong>' . $this->site->getWord('tournament_bonus') . '</strong></th>
			<th><strong>' . $this->site->getWord('tournament_bonus_description') . '</strong></th>
			</tr>';

		foreach ($content as $bonus)
		{
			$descr = isset ($bonus['bonus_description']) ?
							$bonus['bonus_description'] :
							'<span class="faded">unknown</span>';
			
			$player = '<a href="player.php?id=' . $bonus['player_id'] . '">' . $bonus['name_pokerstars'] . '</a>';
			
			$out .=
			'<tr>
				<td>' . $player . '</td>
				<td>' . $bonus['bonus_value'] . '</td>
				<td>' . $descr . '</td>
			</tr>';	
		}

		$out .= '</table>';
		
		return $out;
	}
}