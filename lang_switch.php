<?php

require_once 'autoload.php';
use FileListPoker\Main\Config;

if ($_GET['lang'] !== 'ro' && $_GET['lang'] !== 'en')
{
	header('Location: index.php');
}

$config = Config::getConfig();
$cookieName = $config->getValue('lang_cookie_name');
$cookieDuration = $config->getValue('lang_cookie_duration');
	
setcookie($cookieName, $_GET['lang'], time() + $cookieDuration);

$redirpage = '';
switch($_GET['returnpage'])
{
	case 'index.php':			$redirpage = 'index.php'; break;
	case 'players.php':			$redirpage = 'players.php'; break;
	case 'tournaments.php':		$redirpage = 'tournaments.php'; break;
	case 'rankings.php':		$redirpage = 'rankings.php'; break;
	case 'statistics.php':		$redirpage = 'statistics.php'; break;
	case 'players.month.php':	$redirpage = 'players.month.php'; break;
	case 'contact.php':			$redirpage = 'contact.php'; break;
	default:					$redirpage = 'index.php';
}

header("Location: $redirpage");