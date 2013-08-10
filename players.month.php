<?php

require_once 'Site.class.php';

require_once 'PlayersMonthPage.class.php';

$site = new Site();

$htmlout = $site->getHeader('players.month.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_players_of_the_month') . '</div>';

$htmlout .= '<div id="content">';

$playersMonthPage = new PlayersMonthPage();
$content = $playersMonthPage->getContent();

$htmlout .= '<table class="presentation-table" style="width:100%">
			<tr>
			<th><strong>' . $site->getWord('players_month_pokerstars') . '</strong></th>
			<th><strong>' . $site->getWord('players_month_filelist') . '</strong></th>
			<th><strong>' . $site->getWord('players_month_date') . '</strong></th>
			</tr>';

foreach ($content as $award)
{
	$awardTime = mktime(0, 0, 0, $award['month'], 2, $award['year']);
	$awardDate = date('F Y', $awardTime);
	
	$htmlout .=
	'<tr>
		<td><a href="player.php?id=' . $award['id'] . '">' . $award['name_pokerstars'] . '</a></td>
		<td><a href="http://filelist.ro/userdetails.php?id=' . $award['id_filelist'] . '">' . $award['name_filelist'] . '</a></td>
		<td>' . $awardDate . '</td>
	</tr>';	
}

$htmlout .= '</table>';

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;