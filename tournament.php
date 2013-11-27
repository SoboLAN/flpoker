<?php

require_once 'autoload.php';

use FileListPoker\Pages\TournamentPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\TournamentRenderer;

$site = new Site();

$htmlout = $site->getHeader('tournaments.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_tournaments') . '</div>';

$htmlout .= '<div id="content-narrower">';

if (! isset($_GET['id']))
{
    die('You must specify a tournament ID');
}
//eliminate some junk... (people can put all sorts of stuff in this thing)...
else if (strlen ($_GET['id']) > 4 ||
        ! is_numeric ($_GET['id']) ||
        strpos ($_GET['id'], '.') !== FALSE ||
        strpos ($_GET['id'], "'") !== FALSE)
{
    die('Invalid tournament ID');
}

$tid = $_GET['id'];

$tournamentPage = new TournamentPage();
$details = $tournamentPage->getTournamentDetails($tid);
$results = $tournamentPage->getTournamentResults($tid);
$bonuses = $tournamentPage->getTournamentBonuses($tid);

if (! isset($details['id']))
{
    die('Tournament with that ID does not exist');
}

$renderer = new TournamentRenderer($site);

$htmlout .= $renderer->renderDetails($details);

$htmlout .= $renderer->renderResults($results);

$htmlout .= $renderer->renderBonuses($bonuses);

$htmlout .= '</div>';
    
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
    
echo $htmlout;