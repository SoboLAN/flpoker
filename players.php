<?php

require_once 'autoload.php';

use FileListPoker\Pages\PlayersPage;
use FileListPoker\Main\Site;
use FileListPoker\Renderers\PlayersRenderer;
use FileListPoker\Main\FLPokerException;

try {
    $site = new Site();

    $htmlout = $site->getHeader('players.php');

    $htmlout .= '<div id="title">' . $site->getWord('menu_players') . '</div>
                <div id="content-narrower">';

    $playersPage = new PlayersPage();
    $content = $playersPage->getContent();
    
    $renderer = new PlayersRenderer($site);

    $htmlout .= $renderer->render($content);
    
} catch (FLPokerException $ex) {
    switch ($ex->getType()) {
        case FLPokerException::ERROR:
            header('Location: 500.shtml');
            break;
        case FLPokerException::INVALID_REQUEST:
            header('Location: 400.shtml');
            break;
        case FLPokerException::SITE_DOWN:
            header('Location: maintenance.shtml');
            break;
        default:
            header('Location: 500.shtml');
    }
}

$htmlout .= '<p><span style="font-size:15px; font-family:Tahoma; background-color:#96EC2D; ' .
            'padding-left:40px; border:1px solid black; overflow:hidden">&nbsp;</span> ' .
            '&#61; FileList Poker Administrator</p>';

$htmlout .= '</div>';
    
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
    
echo $htmlout;