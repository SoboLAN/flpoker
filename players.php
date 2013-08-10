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
			<th><strong>Nr.</strong></th>
			<th><strong>' . $site->getWord('players_pokerstars_name') . '</strong></th>
			<th><strong>' . $site->getWord('players_filelist_name') . '</strong></th>
			<th><strong>' . $site->getWord('players_registration_date') . '</strong></th>
			<th><strong>' . $site->getWord('players_current_points') . '</strong></th>
			</tr>';

$i = 1;
foreach ($content as $player)
{
	if (is_null($player['name_pokerstars']))
	{
		$regDate = '';
	}
	else
	{
		$regTime = mktime(0, 0, 0, $player['month'], $player['day'], $player['year']);
		$regDate = date('j F Y', $regTime);
	}
	
	$htmlout .=
	'<tr' . ($player['member_type'] == 'admin' ? ' class="admin-marker"' : '') . '>
		<td>' . $i . '</td>
		<td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
		<td><a href="http://filelist.ro/userdetails.php?id=' . $player['id_filelist'] . '">' . $player['name_filelist'] . '</a></td>
		<td>' . $regDate . '</td>
		<td>' . $player['points'] . '</td>
	</tr>';
	
	$i++;
}

$htmlout .= '</table>';

$htmlout .= '<p><span style="font-size:15px; font-family:Tahoma; background-color:#96EC2D; ' .
			'padding-left:40px; border:1px solid black; overflow:hidden">&nbsp;</span> ' .
			'&#61; FileList Poker Administrator</p>';

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;