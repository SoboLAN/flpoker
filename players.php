<?php

require_once 'Site.class.php';

require_once 'PlayersPage.class.php';

$site = new Site();

$htmlout = $site->getHeader('players.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_players') . '</div>';

$htmlout .= '<div id="content">';

$excelPage = new PlayersPage();
$content = $excelPage->getContent();

$htmlout .= '<table class="presentation-table" style="width:100%">
			<tr>
			<th><strong>' . $site->getWord('players_pokerstars_name') . '</strong></th>
			<th><strong>' . $site->getWord('players_filelist_name') . '</strong></th>
			<th><strong>' . $site->getWord('players_current_points') . '</strong></th>
			</tr>';

foreach ($content as $player)
{
	$htmlout .= '<tr>
				<td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
				<td><a href="http://filelist.ro/userdetails.php?id=' . $player['id_filelist'] . '">' . $player['name_filelist'] . '</a></td>
				<td>' . $player['points'] . '</td>
				</tr>';	
}

$htmlout .= '</table>';

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;