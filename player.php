<?php

require_once 'Site.class.php';

$site = new Site();

function getGeneral($info)
{
	global $site;

	$regDate = date('j F Y', mktime(0, 0, 0, $info['month'], $info['day'], $info['year']));
	
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

function getTournamentHistory($history)
{
	global $site;

	$out = '<table class="presentation-table" style="width:90%">
			<tr>
			<th><strong>' . $site->getWord('player_tournament_tournament') . '</strong></th>
			<th><strong>' . $site->getWord('player_tournament_points') . '</strong></th>
			<th><strong>' . $site->getWord('player_tournament_position') . '</strong></th>
			</tr>';

	foreach ($history as $tournament)
	{
		$tTime = mktime(0, 0, 0, $tournament['month'], $tournament['day'], $tournament['year']);
		$tDate = date('j F Y', $tTime);
		
		$out .=
		'<tr>
			<td><a href="tournament.php?id=' . $tournament['tournament_id'] . '">' . $tDate . '</a></td>
			<td>' . $tournament['points'] . '</a></td>
			<td>' . $tournament['position'] . '</td>
		</tr>';	
	}

	$out .= '</table>';
	
	return $out;
}

function getBonuses($bonuses)
{
	global $site;

	$out = '<table class="presentation-table" style="width:90%">
			<tr>
			<th><strong>' . $site->getWord('player_bonus_tournament') . '</strong></th>
			<th><strong>' . $site->getWord('player_bonus_date') . '</strong></th>
			<th><strong>' . $site->getWord('player_bonus_value') . '</strong></th>
			<th><strong>' . $site->getWord('player_bonus_description') . '</strong></th>
			</tr>';

	foreach ($bonuses as $bonus)
	{
		$bDate = date('j F Y', mktime(0, 0, 0, $bonus['month'], $bonus['day'], $bonus['year']));
		
		$out .=
		'<tr>
			<td><a href="tournament.php?id=' . $bonus['tournament_id'] . '">' . $bonus['tournament_id'] . '</a></td>
			<td>' . $bDate . '</a></td>
			<td>' . $bonus['bonus_value'] . '</a></td>
			<td>' . $bonus['description'] . '</td>
		</tr>';	
	}

	$out .= '</table>';
	
	return $out;
}

function getPrizes($prizes)
{
	global $site;

	$out = '<table class="presentation-table" style="width:90%">
			<tr>
			<th><strong>' . $site->getWord('player_prize_prize') . '</strong></th>
			<th><strong>' . $site->getWord('player_prize_date') . '</strong></th>
			<th><strong>' . $site->getWord('player_prize_cost') . '</strong></th>
			</tr>';

	foreach ($prizes as $prize)
	{
		if (is_null($prize['day']) OR is_null($prize['month']) OR is_null($prize['year']))
		{
			$pDate = '<span class="faded">unknown</span>';
		}
		else
		{
			$pDate = date('j F Y', mktime(0, 0, 0, $prize['month'], $prize['day'], $prize['year']));
		}
		
		$out .=
		'<tr>
			<td>' . $prize['prize'] . '</a></td>
			<td>' . $pDate . '</a></td>
			<td>' . $prize['cost'] . '</td>
		</tr>';	
	}

	$out .= '</table>';
	
	return $out;
}


$htmlout = $site->getHeader('players.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_players') . '</div>';

$htmlout .= '<div id="content-narrower">';

if (! isset($_GET['id']))
{
	die('You must specify a player ID');
}
//eliminate some junk... (people can put all sorts of crap in this thing)...
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

$general = getGeneral($playerPage->getGeneral($player_id));
$thistory = getTournamentHistory($playerPage->getTournamentHistory($player_id));
$bonuses = getBonuses($playerPage->getBonuses($player_id));
$prizes = getPrizes($playerPage->getPrizes($player_id));

$htmlout .=
	'<div id="tabs">
		<ul>
			<li><a href="#tabs-1">' . $site->getWord('player_tab_general_title') . '</a></li>
			<li><a href="#tabs-2">' . $site->getWord('player_tab_thistory_title') . '</a></li>
			<li><a href="#tabs-3">' . $site->getWord('player_tab_bonuses_title') . '</a></li>
			<li><a href="#tabs-4">' . $site->getWord('player_tab_prizes_title') . '</a></li>
		</ul>
		<div id="tabs-1">
			<p>' . $general . '</p>
		</div>
		<div id="tabs-2">
			<p>' . $thistory . '</p>
		</div>
		<div id="tabs-3">
			<p>' . $bonuses . '</p>
		</div>
		<div id="tabs-4">
			<p>' . $prizes . '</p>
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