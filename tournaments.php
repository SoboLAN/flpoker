<?php

require_once 'autoload.php';

use FileListPoker\Pages\TournamentsPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\TournamentsRenderer;

$site = new Site();

$htmlout = $site->getHeader('tournaments.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_tournaments') . '</div>';

$htmlout .= '<div id="content-narrower">';

$tournamentsPage = new TournamentsPage();
$content = $tournamentsPage->getContent();

$renderer = new TournamentsRenderer($site);

$htmlout .= $renderer->render($content);

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;