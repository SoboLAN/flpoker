<?php
require_once 'DB.class.php';
require_once 'Site.class.php';

class PlayerPage
{
	public function __construct()
	{
	
	}
	
	public function getContent()
	{
		if (Config::getConfig()->getValue('enable_cache'))
		{
			// TODO: implement this
		}
		
		$db = Database::getConnection();

		try
		{
			
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries');
		}
	}
}