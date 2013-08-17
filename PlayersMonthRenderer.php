<?php

require_once 'GeneralRenderer.php';
require_once 'Site.class.php';

class PlayersMonthRenderer extends GeneralRenderer
{
	private $site;
	
	public function __construct(Site $site)
	{
		$this->site = $site;
	}
	
	public function render($content)
	{
		$out = '<table class="presentation-table" style="width:100%">
			<tr>
			<th><strong>' . $this->site->getWord('players_month_pokerstars') . '</strong></th>
			<th><strong>' . $this->site->getWord('players_month_filelist') . '</strong></th>
			<th><strong>' . $this->site->getWord('players_month_date') . '</strong></th>
			</tr>';

		foreach ($content as $award)
		{
			$awardTime = mktime(0, 0, 0, $award['month'], 2, $award['year']);
			$awardDate = date('F Y', $awardTime);
			
			if ($this->site->getLanguage() !== 'en')
			{
				$awardDate = $this->translateDate($awardDate, $this->site->getLanguage());
			}

			$out .=
			'<tr>
				<td><a href="player.php?id=' . $award['id'] . '">' . $award['name_pokerstars'] . '</a></td>
				<td><a href="http://filelist.ro/userdetails.php?id=' . $award['id_filelist'] . '">' . $award['name_filelist'] . '</a></td>
				<td>' . $awardDate . '</td>
			</tr>';	
		}

		$out .= '</table>';
		
		return $out;
	}
}