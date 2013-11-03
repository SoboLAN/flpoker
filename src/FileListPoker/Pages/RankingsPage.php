<?php

namespace FileListPoker\Pages;

use FileListPoker\Main\Database;
use FileListPoker\Main\Config;
use FileListPoker\Main\CacheDB;
use FileListPoker\Main\CacheFile;

class RankingsPage
{
	private $cache;
	
	public function __construct()
	{
		if (Config::getConfig()->getValue('enable_cache'))
		{
			$cacheType = Config::getConfig()->getValue('cache_type');
		
			if($cacheType == 'db')
			{
				$this->cache = new CacheDB();
			}
			elseif ($cacheType == 'file')
			{
				$this->cache = new CacheFile();
			}
		}
	}
	
	public function getTopPlayersAllTime()
	{
		if (! is_null ($this->cache))
		{
			$key = Config::getConfig()->getValue('cache_key_players_alltime');
			
			if ($this->cache->contains ($key))
			{
				$content = json_decode ($this->cache->getContent($key), true);
				
				return $content;
			}
		}
		
		$db = Database::getConnection()->getPDO();
		
		//the formula is initial_accumulated_points + initial_prizes + results + bonuses
		try
		{
			$players = $db->query ('SELECT player_id, id_filelist, name_pokerstars, name_filelist,
								initial_accumulated_points, initial_spent_points ' .
								'FROM players ' .
								'ORDER BY player_id ASC');

			$tmpresults = $db->query('SELECT SUM(points) AS points, player_id ' .
								'FROM results ' .
								'GROUP BY player_id ' .
								'ORDER BY player_id ASC');

			$tmpbonuses = $db->query('SELECT SUM(bonus_value) AS bonus_value, player_id ' .
								'FROM bonus_points ' .
								'GROUP BY player_id ' .
								'ORDER BY player_id ASC');
		}
		catch (\PDOException $e)
		{
			die('There was a problem while performing database queries');
		}
		
		$results = array();
		$bonuses = array();
		foreach ($tmpresults as $tmpresult)
		{
			$results[] = $tmpresult;
		}
		foreach ($tmpbonuses as $tmpbonus)
		{
			$bonuses[] = $tmpbonus;
		}

		$final_result = array();
		
		foreach ($players as $player)
		{
			$currentResults = $this->array_binary_search($results, 'points', $player->player_id);
			$currentBonuses = $this->array_binary_search($bonuses, 'bonus_value', $player->player_id);

			$playerPoints = $player->initial_accumulated_points + $player->initial_spent_points +
							$currentResults + $currentBonuses;
			
			$final_result[] = array('player_id' => $player->player_id,
									'id_filelist' => $player->id_filelist,
									'name_pokerstars' => $player->name_pokerstars,
									'name_filelist' => $player->name_filelist,
									'points' => $playerPoints
			);
		}
		
		$this->array_sort_by_column($final_result, 'points');
		
		if (! is_null ($this->cache))
		{
			$key = Config::getConfig()->getValue('cache_key_players_alltime');
			
			$lifetime = Config::getConfig()->getValue('cache_lifetime_players_alltime');
			
			$this->cache->save($key, json_encode($final_result), $lifetime);
		}
		
		return $final_result;
	}
	
	public function getMostActive50Players()
	{
		if (! is_null ($this->cache))
		{
			$key = Config::getConfig()->getValue('cache_key_players_mostactive');
			
			if ($this->cache->contains ($key))
			{
				$content = json_decode ($this->cache->getContent($key), true);
				
				return $content;
			}
		}
		
		$db = Database::getConnection()->getPDO();
		
		try
		{
			$tmpactive = $db->query(
			'SELECT r.count, p.name_pokerstars, p.player_id FROM ' .
			'(SELECT COUNT(*) AS count, player_id FROM results ' .
			'WHERE player_id IS NOT NULL ' .
			'GROUP BY player_id ' .
			'ORDER BY count DESC) r ' .
			'JOIN players p ON p.player_id=r.player_id ' .
			'LIMIT 50');
		}
		catch (\PDOException $e)
		{
			die('There was a problem while performing database queries');
		}
		
		$results = array();
		foreach($tmpactive as $r)
		{
			$results[] = array('player_id' => $r->player_id,
								'name_pokerstars' => $r->name_pokerstars,
								'count' => $r->count
			);
		}
		
		if (! is_null ($this->cache))
		{
			$key = Config::getConfig()->getValue('cache_key_players_mostactive');
			
			$lifetime = Config::getConfig()->getValue('cache_lifetime_players_mostactive');
			
			$this->cache->save($key, json_encode($results), $lifetime);
		}
		
		return $results;
	}
	
	public function getTop40Players6Months()
	{
		if (! is_null ($this->cache))
		{
			$key = Config::getConfig()->getValue('cache_key_players_6months');
			
			if ($this->cache->contains ($key))
			{
				$content = json_decode ($this->cache->getContent($key), true);
				
				return $content;
			}
		}
		
		$db = Database::getConnection()->getPDO();
		
		try
		{
			$tmp6months = $db->query(
			'SELECT r.player_id, SUM(r.points) AS totalp, p.name_pokerstars ' .
			'FROM results r ' .
			'JOIN tournaments t ON r.tournament_id = t.tournament_id ' .
			'JOIN players p ON r.player_id = p.player_id ' .
			'WHERE DATEDIFF(CURDATE(), tournament_date) <= 30 *6 ' .
			'GROUP BY r.player_id ' .
			'ORDER BY totalp DESC ' .
			'LIMIT 40');
			
		}
		catch (\PDOException $e)
		{
			die('There was a problem while performing database queries');
		}
		
		$results = array();
		foreach($tmp6months as $r)
		{
			$results[] = array('player_id' => $r->player_id,
								'name_pokerstars' => $r->name_pokerstars,
								'totalp' => $r->totalp
			);
		}
		
		if (! is_null ($this->cache))
		{
			$key = Config::getConfig()->getValue('cache_key_players_6months');
			
			$lifetime = Config::getConfig()->getValue('cache_lifetime_players_6months');
			
			$this->cache->save($key, json_encode($results), $lifetime);
		}
		
		return $results;
	}
	
	private function array_sort_by_column(&$arr, $col, $dir = SORT_DESC)
	{
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
	}
	
	private function array_binary_search($array, $type, $elem) 
	{
		$top = sizeof($array) - 1;
		$bot = 0;
		while($top >= $bot) 
		{
			$p = floor(($top + $bot) / 2);
			if ($array[$p]->player_id < $elem)
			{
				$bot = $p + 1;
			}
			elseif ($array[$p]->player_id > $elem)
			{
				$top = $p - 1;
			}
			else
			{
				return $array[$p]->$type;
			}
		}
	   
		return 0;
	}
}