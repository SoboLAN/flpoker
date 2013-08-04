<?php
	require_once 'config/site_config.php';
	
	if ($_GET['lang'] !== 'ro' && $_GET['lang'] !== 'en')
	{
		header('Location: index.php');
	}
	
	setcookie('sitelang', $_GET['lang'], $siteConfig['lang_cookie_duration']);
	
	header('Location: index.php');