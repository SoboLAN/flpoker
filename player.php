<?php

require_once 'Site.class.php';
require_once 'PlayerPage.php';
require_once 'PlayerRenderer.php';

$site = new Site();

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

$playerPage = new PlayerPage();

$general = $playerPage->getGeneral($player_id);

if (! isset($general['points']) OR empty($general['points']))
{
	die('There is no player with that ID.');
}

$renderer = new PlayerRenderer($site);

$general = $renderer->renderGeneral($general);
$thistory = $renderer->rendererTHistory($playerPage->getTournamentHistory($player_id));
$bonuses = $renderer->renderBonuses($playerPage->getBonuses($player_id));
$prizes = $renderer->renderPrizes($playerPage->getPrizes($player_id));

$htmlout .=
	'<div id="tabs">
		<ul>
			<li><a href="#tabs-1">' . $site->getWord('player_tab_general_title') . '</a></li>
			<li><a href="#tabs-2">' . $site->getWord('player_tab_thistory_title') . '</a></li>
			<li><a href="#tabs-3">' . $site->getWord('player_tab_bonuses_title') . '</a></li>
			<li><a href="#tabs-4">' . $site->getWord('player_tab_prizes_title') . '</a></li>
		</ul>
		<div id="tabs-1">
			' . $general . '
		</div>
		<div id="tabs-2">
			' . $thistory . '
		</div>
		<div id="tabs-3">
			' . $bonuses . '
		</div>
		<div id="tabs-4">
			' . $prizes . '
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