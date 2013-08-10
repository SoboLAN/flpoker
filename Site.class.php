<?php
require_once 'Config.class.php';

class Site
{
	private static $availableLangs = array('en', 'ro');
	
	private static $jQueryDependency = array (
		'index.php'			=> false,
		'statistics.php'	=> true,
		'players.php'		=> true,
		'tournaments.php'	=> true,
		'rules.php'			=> false,
		'ask.prize.php'		=> true,
		'contact.php'		=> false,
	);
	
	private static $highChartsDependency = array (
		'index.php'			=> false,
		'statistics.php'	=> true,
		'players.php'		=> true,
		'tournaments.php'	=> true,
		'rules.php'			=> false,
		'ask.prize.php'		=> false,
		'contact.php'		=> false,
	);
	
	private $lang;
	private $wording;
	
	public function __construct ()
	{
		if(! Config::getConfig()->getValue('online'))
		{
			die ('The site is currently down for maintenance. Will be back at ' . $this->config['online_eta']);
		}
		
		//wording must be AFTER language, because wording is filled based on language
		$this->fillLanguage ();
		$this->fillWording ();
	}
	
	private function fillWording ()
	{
		require_once 'wording/text.' . $this->lang . '.php';

		$this->wording = $siteLabels;
	}
	
	private function fillLanguage ()
	{
		$cookiename = Config::getConfig()->getValue('lang_cookie_name');
	
		if (isset ($_COOKIE[$cookiename]))
		{
			$userlang = $_COOKIE[$cookiename];
			
			if (in_array($userlang, self::$availableLangs))
			{
				$this->lang = $userlang;
			}
		}
		else
		{
			$this->lang = Config::getConfig()->getValue('default_lang');
		}
	}
	
	public function getLanguage ()
	{
		return $this->lang;
	}
	
	public function getWord ($key)
	{
		return $this->wording[$key];
	}
	
	//one of the very few functions that contain template information (the other one is getFooter)
	//not a good practice, but I made an exception this time
	public function getHeader ($page = 'index.php')
	{
		$pageTitle = '';
		switch($page)
		{
			case 'index.php': 		$pageTitle = $this->wording['menu_home']; break;
			case 'statistics.php': 	$pageTitle = $this->wording['menu_statistics']; break;
			case 'players.php': 	$pageTitle = $this->wording['menu_players']; break;
			case 'tournaments.php': $pageTitle = $this->wording['menu_tournaments']; break;
			case 'rules.php': 		$pageTitle = $this->wording['menu_rules']; break;
			case 'ask.prize.php':	$pageTitle = $this->wording['menu_askprize']; break;
			case 'contact.php':		$pageTitle = $this->wording['menu_contact']; break;
			default: die('Invalid Page');
		}
		
		$out = '<!DOCTYPE html>
		<html>
		<head>
		<meta charset="utf-8">
		<title>FileList Poker Points - ' . $pageTitle . ' (JavaFling)</title>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<link rel="shortcut icon" href="favicon.ico" />';
		
		if (self::$jQueryDependency[$page])
		{
			$out .= '<script src="js/jquery-1.9.1.js"></script>';
			$out .= '<script src="js/jquery-ui-1.10.3.custom.min.js"></script>';
			$out .= '<link rel="stylesheet" href="css/dark-hive/jquery-ui-1.10.3.custom.min.css" />';

			if (self::$highChartsDependency[$page])
			{
				$out .= '<script src="js/highcharts/highcharts.js"></script>';
				$out .= '<script src="js/highcharts/modules/exporting.js"></script>';
				$out .= '<script src="js/highcharts/themes/dark-blue.js"></script>';
			}
		}
		
		
		$out .= '</head><body>';
		
		if (Config::getConfig()->getValue('enable_google_analytics'))
		{
			require_once 'analytics.google.php';
			
			$out .= getAnalyticsScript();
		}
		
		$out .= '<div id="container">
			<ul class="claybricks">';
		
		$out .= '<li><a href="index.php" ' . (($page == 'index.php') ? 'class="selected">' : '>') .
				$this->wording['menu_home'] . '</a></li>';
		
		$out .= '<li><a href="statistics.php" ' . (($page == 'statistics.php') ? 'class="selected">' : '>') .
				$this->wording['menu_statistics'] . '</a></li>';
		
		$out .= '<li><a href="players.php" ' . (($page == 'players.php') ? 'class="selected">' : '>') .
				$this->wording['menu_players'] . '</a></li>';
		
		$out .= '<li><a href="tournaments.php" ' . (($page == 'tournaments.php') ? 'class="selected">' : '>') .
				$this->wording['menu_tournaments'] . '</a></li>';
		
		$out .= '<li><a href="rules.php" ' . (($page == 'rules.php') ? 'class="selected">' : '>') .
				$this->wording['menu_rules'] . '</a></li>';
		
		$out .= '<li><a href="ask.prize.php" ' . (($page == 'ask.prize.php') ? 'class="selected">' : '>') .
				$this->wording['menu_askprize'] . '</a></li>';

		$out .= '</ul>
		<p id="language_panel">';
		
		if ($this->lang == 'ro')
		{
			$out .= '
				<img class="active_lang" src="images/ro.gif" title="' . $this->wording['langpanel_ro'] .'" />
			<a href="lang_switch.php?lang=en">
				<img src="images/us.gif" title="' . $this->wording['langpanel_en_switch'] . '" />
			</a>';
		}
		else if ($this->lang == 'en')
		{
			$out .= '
			<a href="lang_switch.php?lang=ro">
				<img src="images/ro.gif" title="' . $this->wording['langpanel_ro_switch'] .'" />
			</a>
				<img class="active_lang" src="images/us.gif" title="' . $this->wording['langpanel_en'] . '" />
			';
		}
			
		$out .= '</p>';
		
		return $out;
	}

	public function getFooter ()
	{
		$out = '<div id="footer">
			Copyright &copy; 2013 Radu Murzea.
			<br />
			Project Author and Site Design: Radu Murzea.
			<div id="contact-button">
					<a href="contact.php"><img alt="Contact" src="images/contact-button.jpg" /></a>
				</div>
		</div>';
		
		$out .= '</div>'; //closing id="container" too
		
		return $out;
	}
}