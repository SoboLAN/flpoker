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

header('Location: index.php');