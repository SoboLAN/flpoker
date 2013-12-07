<?php
require_once 'autoload.php';
use FileListPoker\Main\Site;
use FileListPoker\Main\FLPokerException;

try {
    $site = new Site();

    $htmlout = $site->getFullPageTemplate('index.php');

} catch (FLPokerException $ex) {
    switch ($ex->getType()) {
        case FLPokerException::ERROR:
            header('Location: 500.shtml');
			exit();
            break;
        case FLPokerException::SITE_OFFLINE:
            header('Location: maintenance.shtml');
			exit();
            break;
        default:
            header('Location: 500.shtml');
			exit();
    }
}

if ($site->getLanguage() == 'ro')
{
    $text =
    '<article><p>Acest site conține toate informațiile legate de clubul de poker FileList.
    Aici puteți găsi tot: de la numărul de puncte al unui jucător, până la evoluția în timp a
    numărului de participanți a turneelor; de la statistici legate de premii și bonus-uri până la
    totalul punctelor cheltuite de către toți membrii.</p>
    <p>Puteți răsfoi în voie. Enjoy.</p></article>';
}
else if ($site->getLanguage() == 'en')
{
    $text =
    '<article><p>This site contains all the informations regarding the FileList poker club.
    You can find everything here: the number of points of a player, the evolution in time of the
    total number of players registered in a tournament. You can even find statistics about bonuses,
    prizes and the total number of points spent by the club members.</p>
    <p>You may browse to any page. Enjoy.</p></article>';
}

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{bottom_page_scripts}'),
    array('content', $text, ''),
    $htmlout
);

echo $htmlout;
