<?php
class Cache
{
	public static $classicViewKey = 'classic_view';
	public static $topPlayersSixMonthsKey = 'top_players_6_months';
	public static $topPlayersAllTimeKey = 'top_players_all_time';
	public static $topPlayersBonusKey = 'top_players_bonus';
	public static $topFivePlayersMonthKey = 'top_five_players_month';

	private static $classicViewLifetime = 3600 x 12;
	private static $topPlayersSixMonthsLifetime = 3600 x 12;
	private static $topPlayersAllTimeLifetime = 3600 x 12;
	private static $topPlayersBonusLifetime = 3600 x 24;
	private static $topFivePlayersMonthLifetime = 3600 x 12;

	public function isInCache($key)
	{
	
	}

	public function saveToCache($key, $content)
	{
	
	}
}