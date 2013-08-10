<?php

require_once 'Site.class.php';

require_once 'TournamentsPage.class.php';

$site = new Site();

$htmlout = $site->getHeader('tournaments.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_tournaments') . '</div>';

$htmlout .= '<div id="content">';

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
	$typeLabelKey = $tournament['tournament_type'] == 'regular' ? 'tournaments_regular' : 'tournaments_special';
	
	$htmlout .=
	'<tr>
		<td>' . $tournament['tournament_date'] . '</td>
		<td>' . $site->getWord($typeLabelKey) . '</td>
		<td>' . $tournament['participants'] . '</td>
		<td><a href="tournament.php?id=' . $tournament['tournament_id'] . '">' . $site->getWord('tournaments_more_details') . '</a></td>
	</tr>';	
}

$htmlout .= '</table>';

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;