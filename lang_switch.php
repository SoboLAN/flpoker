<?php
require_once 'Config.class.php';

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
	case 'statistics.php':		$redirpage = 'statistics.php'; break;
	case 'players.php':			$redirpage = 'players.php'; break;
	case 'tournaments.php':		$redirpage = 'tournaments.php'; break;
	case 'players.month.php':	$redirpage = 'players.month.php'; break;
	case 'rules.php':			$redirpage = 'rules.php'; break;
	case 'ask.prize.php':		$redirpage = 'ask.prize.php'; break;
	case 'contact.php':			$redirpage = 'contact.php'; break;
	default:					$redirpage = 'index.php';
}

header("Location: $redirpage");