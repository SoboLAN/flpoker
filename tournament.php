<?php

require_once 'Site.class.php';

require_once 'DAO/TournamentPage.php';

$site = new Site();

$htmlout = $site->getHeader('tournaments.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_tournaments') . '</div>';

$htmlout .= '<div id="content-narrower">';

if (! isset($_GET['id']))
{
	die('You must specify a tournament ID');
}
//eliminate some junk... (people can put all sorts of stuff in this thing)...
else if (strlen ($_GET['id']) > 4 || ! is_numeric ($_GET['id']) || strpos ($_GET['id'], '.') !== FALSE)
{
	die('Invalid tournament ID');
}

$tid = $_GET['id'];

$tournamentPage = new TournamentPage();
$details = $tournamentPage->getTournamentDetails($tid);

if (! isset($details['id']))
{
	die('Tournament with that ID does not exist');
}

$tournamentTime = mktime(0, 0, 0, $details['month'], $details['day'], $details['year']);
$tournamentDate = date('l, jS F Y', $tournamentTime);

$htmlout .=
	'<p>
		<span class="subtitle">Details</span>
	</p>
	<p>
		<span class="bigger_label">Tournament Date: ' . $tournamentDate . '</span>
	</p>
	<p>
		<span class="bigger_label">Tournament Type: ' . $details['type'] . '</span>
	</p>
	<p>
		<span class="bigger_label">Participants: ' . $details['participants'] . '</span>
	</p>
	<p>
		<span class="subtitle">Results</span>
	</p>';

$results = $tournamentPage->getTournamentResults($tid);

$htmlout .= '<table class="presentation-table" style="width:100%">
			<tr>
			<th><strong>' . $site->getWord('tournament_player') . '</strong></th>
			<th><strong>' . $site->getWord('tournament_points') . '</strong></th>
			<th><strong>' . $site->getWord('tournament_position') . '</strong></th>
			</tr>';

foreach ($results as $result)
{
	if (isset ($result['player_id']) AND isset ($result['name_pokerstars']))
	{
		$player = '<a href="player.php?id=' . $result['player_id'] . '">' . $result['name_pokerstars'] . '</a>';
	}
	else
	{
		$player = '<span class="faded">unknown</span>';
	}
	
	$htmlout .=
	'<tr>
		<td>' . $player . '</td>
		<td>' . $result['points'] . '</td>
		<td>' . $result['position'] . '</td>
	</tr>';	
}

$htmlout .= '</table>';

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;