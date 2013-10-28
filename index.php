<?php
require_once 'autoload.php';
use FileListPoker\Main\Site;

$site = new Site();
	
$htmlout = $site->getHeader();
	
$htmlout .= '<div id="title">' . $site->getWord('menu_home') . '</div>';
	
$htmlout .= '<div id="content">';
	
if ($site->getLanguage() == 'ro')
{
	$htmlout .=
	'<p>Acest site conține toate informațiile legate de clubul de poker FileList.
	Aici puteți găsi tot: de la numărul de puncte al unui jucător, până la evoluția în timp a
	numărului de participanți a turneelor; de la statistici legate de premii și bonus-uri până la
	totalul punctelor cheltuite de către toți membrii.</p>
	<p>Puteți răsfoi în voie. Enjoy.</p>';
}
else if ($site->getLanguage() == 'en')
{
	$htmlout .=
	'<p>This site contains all the informations regarding the FileList poker club.
	You can find everything here: the number of points of a player, the evolution in time of the
	total number of players registered in a tournament. You can even find statistics about bonuses,
	prizes and the total number of points spent by the club members.</p>
	<p>You may browse to any page. Enjoy.</p>';
}
	
$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;



	
//the general template looks like this: (presented just as guideline):

/*
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>FileList Poker - Punctaje</title>
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
	<div id="container">
	
		<ul class="claybricks">
			<li><a href="page1.php" class="selected">Home Page</a></li>
			<li><a href="page1.php">Classic View</a></li>
			<li><a href="page1.php">Statistics</a></li>
			<li><a href="page1.php">Players</a></li>
			<li><a href="page1.php">Tournaments</a></li>
			<li><a href="page1.php">Rules</a></li>
			<li><a href="page1.php">Ask for Prize</a></li>
		</ul>
		
		<p id="language_panel">
			<a href="lang_switch.php?lang=ro"><img  src="images/ro.gif" title="Switch to Romanian" /></a>
			<a href="lang_switch.php?lang=en"><img class="active_lang" src="images/us.gif" title="Switch to English" /></a>
		</p>
		
		<div id="title">
				Home
		</div>
		
		<div id="content">

			Welcome to FileList Poker Points website.
		
		</div>
		
		<div id="footer">
			Just saying something.		
		</div>
	</div>
</body>
</html>

 */