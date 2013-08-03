<?php
class Cache
{
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
	
	}

	public function saveToCache($key, $content)
	{
	
	}
	
	public function flush($key)
	{
	
	}
}