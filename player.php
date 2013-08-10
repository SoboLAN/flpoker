<?php

require_once 'Site.class.php';

$site = new Site();

$htmlout = $site->getHeader('player.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_players') . '</div>';

$htmlout .= '<div id="content">';

$playerPage = new PlayerPage();
$content = $playerPage->getContent();

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;