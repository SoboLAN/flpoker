<?php

require_once 'autoload.php';
use FileListPoker\Main\Config;

//if language is not OK, go away
if ($_GET['lang'] !== 'ro' && $_GET['lang'] !== 'en')
{
    header('Location: index.php');
}

//language setting is stored in a cookie. so we need parameters for it
$cookieName = Config::getValue('lang_cookie_name');
$cookieDuration = Config::getValue('lang_cookie_duration');

//set the new language in the cookie
setcookie($cookieName, $_GET['lang'], time() + $cookieDuration);

//figure out where the user is so you can send him back to the same page
$redirpage = '';
switch($_GET['returnpage'])
{
    case 'index.php':            $redirpage = 'index.php'; break;
    case 'players.php':            $redirpage = 'players.php'; break;
    case 'tournaments.php':        $redirpage = 'tournaments.php'; break;
    case 'rankings.php':        $redirpage = 'rankings.php'; break;
    case 'statistics.php':        $redirpage = 'statistics.php'; break;
    case 'players.month.php':    $redirpage = 'players.month.php'; break;
    default:                    $redirpage = 'index.php';
}

//redirect the user
header("Location: $redirpage");