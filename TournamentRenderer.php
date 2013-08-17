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
		$tournamentTime = mktime(0, 0, 0, $content['month'], $content['day'], $content['year']);
		$tournamentDate = date('l, jS F Y', $tournamentTime);

		$out =
			'<p>
				<span class="subtitle">Details</span>
			</p>
			<p>
				<span class="bigger_label">Tournament Date: ' . $tournamentDate . '</span>
			</p>
			<p>
				<span class="bigger_label">Tournament Type: ' . $content['type'] . '</span>
			</p>
			<p>
				<span class="bigger_label">Participants: ' . $content['participants'] . '</span>
			</p>
			<p>
				<span class="subtitle">Results</span>
			</p>';
		
		return $out;
	}
	
	public function renderResults($content)
	{
		$out = '<table class="presentation-table" style="width:100%">
			<tr>
			<th><strong>' . $this->site->getWord('tournament_player') . '</strong></th>
			<th><strong>' . $this->site->getWord('tournament_points') . '</strong></th>
			<th><strong>' . $this->site->getWord('tournament_position') . '</strong></th>
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
				<td>' . $player . '</td>
				<td>' . $result['points'] . '</td>
				<td>' . $result['position'] . '</td>
			</tr>';	
		}

		$out .= '</table>';
		
		return $out;
	}
}