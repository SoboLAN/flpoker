<?php
require_once 'config/site.config.php';

class Cache
{
	private static $basedir = 'cache/';
	
	public static $classicViewKey = 'classic_view';
	public static $topPlayersSixMonthsKey = 'top_players_6_months';
	public static $topPlayersAllTimeKey = 'top_players_all_time';
	public static $topPlayersBonusKey = 'top_players_bonus';
	public static $topFivePlayersMonthKey = 'top_five_players_month';

	//will return true or false
	//will tell if the cache with the key $key contains data
	//if it contains and the data is expired, then the data will
	//be deleted and function will return false
	public function isInCache($key)
	{
		$timeFile = self::$basedir . $key . '_timestamp';
		if(!file_exists($timeFile))
		{
			return false;
		}
		
		$oldTimestamp = file_get_contents($timeFile);
		
		if ($this->isTimestampTooOld($oldTimestamp, $siteConfig['']))
		{
			$dataFile = self::$basedir . $key . '_data';
			file_put_contents($dataFile, '');
			
			return false;
		}
		
		$dataFile = self::$basedir . $key . '_data';
		
		return (strlen(file_get_contents($dataFile)) !== 0);
	}
	
	public function getContent($key)
	{
		
	}

	public function saveToCache($key, $content)
	{
	
	}
	
	public function flush($key)
	{
	
	}
	
	private function isTimestampTooOld($timestamp, $lifetime)
	{
		
	}
}