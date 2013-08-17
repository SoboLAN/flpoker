<?php

require_once 'Site.class.php';
require_once 'PlayersPage.php';
require_once 'PlayersRenderer.php';

$site = new Site();

$htmlout = $site->getHeader('players.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_players') . '</div>';

$htmlout .= '<div id="content">';

$playersPage = new PlayersPage();
$content = $playersPage->getContent();

$renderer = new PlayersRenderer($site);

$htmlout .= $renderer->render($content);

$htmlout .= '<p><span style="font-size:15px; font-family:Tahoma; background-color:#96EC2D; ' .
			'padding-left:40px; border:1px solid black; overflow:hidden">&nbsp;</span> ' .
			'&#61; FileList Poker Administrator</p>';

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;