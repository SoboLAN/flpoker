<?php
require_once 'config/site.config.php';
	
if ($_GET['lang'] !== 'ro' && $_GET['lang'] !== 'en')
{
	header('Location: index.php');
}
	
setcookie($siteConfig['lang_cookie_name'], $_GET['lang'], time() + $siteConfig['lang_cookie_duration']);
	
header('Location: index.php');