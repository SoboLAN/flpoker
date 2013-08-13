<?php

require_once 'Site.class.php';

$site = new Site();

function getGeneralTemplate($info)
{
	global $site;
	
	$regTime = mktime(0, 0, 0, $info['month'], $info['day'], $info['year']);
	$regDate = date('j F Y', $regTime);
	
	$out =
	'<p>
		<span class="bigger_label">' . $site->getWord('player_tab_general_pname') . ': ' .
			$info['name_pokerstars'] .
		'</span>
	</p>
	<p>
		<span class="bigger_label">' . $site->getWord('player_tab_general_fname') . ': ' .
			'<a href="http://filelist.ro/userdetails.php?id=' . $info['id_filelist'] . '">' . $info['name_filelist'] . '</a>' .
		'</span>
	</p>
	<p>
		<span class="bigger_label">' . $site->getWord('player_tab_general_regdate') . ': ' .
			$regDate .
		'</span>
	</p>
	<p>
		<span class="bigger_label">' . $site->getWord('player_tab_general_points') . ': ' .
			$info['points'] .
		'</span>
	</p>
	<p>
		<span class="bigger_label">' . $site->getWord('player_tab_general_allpoints') . ': ' .
			$info['points_all_time'] .
		'</span>
	</p>
	<p>
		<span class="bigger_label">' . $site->getWord('player_tab_general_ftables') . ': ' .
			$info['final_tables'] .
		'</span>
	</p>
	<p>
		<span class="bigger_label">' . $site->getWord('player_tab_general_gmedals') . ': ' .
			$info['gold_medals'] .
		'</span>
	</p>
	<p>
		<span class="bigger_label">' . $site->getWord('player_tab_general_smedals') . ': ' .
			$info['silver_medals'] .
		'</span>
	</p>
	<p>
		<span class="bigger_label">' . $site->getWord('player_tab_general_bmedals') . ': ' .
			$info['bronze_medals'] .
		'</span>
	</p>';
	
	return $out;
}

function getTournamentHistory()
{
	//tid
	//tdate
	//points
	//position
	
	global $site;

	$out = '<table class="presentation-table" style="width:90%">
			<tr>
			<th><strong>' . $site->getWord('players_pokerstars_name') . '</strong></th>
			<th><strong>' . $site->getWord('players_filelist_name') . '</strong></th>
			<th><strong>' . $site->getWord('players_points_all_time') . '</strong></th>
			</tr>';

	foreach ($content as $player)
	{
		$out .=
		'<tr>
			<td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
			<td><a href="http://filelist.ro/userdetails.php?id=' . $player['id_filelist'] . '">' . $player['name_filelist'] . '</a></td>
			<td>' . $player['points'] . '</td>
		</tr>';	
	}

	$out .= '</table>';
	
	return $out;
}

function getBonuses()
{
	//total bonuses
}

function getPrizes()
{
	//total prizes
}


$htmlout = $site->getHeader('players.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_players') . '</div>';

$htmlout .= '<div id="content-narrower">';

if (! isset($_GET['id']))
{
	die('You must specify a player ID');
}
//eliminate some junk... (people can put all sorts of stuff in this thing)...
else if (strlen ($_GET['id']) > 4 ||
		! is_numeric ($_GET['id']) ||
		strpos ($_GET['id'], '.') !== FALSE ||
		strpos ($_GET['id'], "'") !== FALSE)
{
	die('Invalid player ID');
}

$player_id = $_GET['id'];

require_once 'DAO/PlayerPage.php';

$playerPage = new PlayerPage();

$general = getGeneralTemplate($playerPage->getGeneral($player_id));

$htmlout .=
	'<div id="tabs">
		<ul>
			<li><a href="#tabs-1">' . $site->getWord('player_tab_general_title') . '</a></li>
			<li><a href="#tabs-2">' . $site->getWord('player_tab_thistory_title') . '</a></li>
			<li><a href="#tabs-3">' . $site->getWord('player_tab_bonuses_title') . '</a></li>
			<li><a href="#tabs-4">' . $site->getWord('player_tab_prizes_title') . '</a></li>
		</ul>
		<div id="tabs-1">
			<p>' . $site->getWord('player_tab_general_text') . '</p>
			<p>' . $general . '</p>
		</div>
		<div id="tabs-2">
			<p>' . $site->getWord('player_tab_thistory_text') . '</p>
			<p>' . '' . '</p>
		</div>
		<div id="tabs-3">
			<p>' . $site->getWord('player_tab_bonuses_text') . '</p>
			<p>' . '' . '</p>
		</div>
		<div id="tabs-4">
			<p>' . $site->getWord('player_tab_prizes_text') . '</p>
			<p>' . '' . '</p>
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