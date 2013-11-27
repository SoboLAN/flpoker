<?php

require_once 'autoload.php';

use FileListPoker\Pages\PlayersMonthPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\PlayersMonthRenderer;

$site = new Site();

$htmlout = $site->getHeader('players.month.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_players_of_the_month') . '</div>';

$htmlout .= '<div id="content-narrower">';

$playersMonthPage = new PlayersMonthPage();
$content = $playersMonthPage->getContent();

$renderer = new PlayersMonthRenderer($site);

$htmlout .= $renderer->render($content);

$htmlout .= '</div>';
    
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
    
echo $htmlout;