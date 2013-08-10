<?php

require_once 'Site.class.php';

require_once 'DAO/TournamentsPage.php';

$site = new Site();

$htmlout = $site->getHeader('tournaments.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_tournaments') . '</div>';

$htmlout .= '<div id="content-narrower">';

$tournamentsPage = new TournamentsPage();
$content = $tournamentsPage->getContent();

$htmlout .= '<table class="presentation-table" style="width:100%">
			<tr>
			<th><strong>' . $site->getWord('tournaments_tournament_date') . '</strong></th>
			<th><strong>' . $site->getWord('tournaments_type') . '</strong></th>
			<th><strong>' . $site->getWord('tournaments_participants') . '</strong></th>
			<th><strong> </strong></th>
			</tr>';

foreach ($content as $tournament)
{
	$typeLabelKey = $tournament['type'] == 'regular' ? 'tournaments_regular' : 'tournaments_special';
	
	$tournamentTime = mktime(0, 0, 0, $tournament['month'], $tournament['day'], $tournament['year']);
	$tournamentDate = date('l, jS F Y', $tournamentTime);
	
	$htmlout .=
	'<tr>
		<td>' . $tournamentDate . '</td>
		<td>' . $site->getWord($typeLabelKey) . '</td>
		<td>' . $tournament['participants'] . '</td>
		<td><a href="tournament.php?id=' . $tournament['id'] . '">' . $site->getWord('tournaments_more_details') . '</a></td>
	</tr>';	
}

$htmlout .= '</table>';

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;