<?php
require_once '/../DB.class.php';
require_once '/../Config.class.php';

class StatisticsPage
{
	public function __construct()
	{
		
	}
	
	public function getTopPlayersAllTime()
	{
		if (Config::getConfig()->getValue('enable_cache'))
		{
			// TODO: implement this
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
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries:' . $e->getMessage());
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
		
		return $final_result;
	}
	
	public function getTournamentsGraph()
	{
		$db = Database::getConnection()->getPDO();
		
		try
		{
			$tmpresults = $db->query(
			'SELECT ext.tournament_id, ext.participants, MONTH(ext.tournament_date) AS month, ' .
			'YEAR(ext.tournament_date) AS year, DAYOFMONTH( ext.tournament_date ) AS day, ' .
				'(SELECT avg(internal.participants) ' .
				'FROM tournaments internal ' .
				'WHERE internal.tournament_id <= ext.tournament_id) AS average_participants ' .
			'FROM tournaments ext ' .
			'ORDER BY tournament_id ASC');
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries:' . $e->getMessage());
		}
		
		$results = array();
		foreach($tmpresults as $r)
		{
			$results[] = array('tournament_id' => $r->tournament_id,
								'participants' => $r->participants,
								'day' => $r->day,
								'month' => $r->month,
								'year' => $r->year,
								'average_participants' => $r->average_participants
			);
		}
		
		return $results;
	}
	
	public function getMostActive50Players()
	{
		if (Config::getConfig()->getValue('enable_cache'))
		{
			// TODO: implement this
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
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries:' . $e->getMessage());
		}
		
		$results = array();
		foreach($tmpactive as $r)
		{
			$results[] = array('player_id' => $r->player_id,
								'name_pokerstars' => $r->name_pokerstars,
								'count' => $r->count
			);
		}
		
		return $results;
	}
	
	public function getTop40Players6Months()
	{
		if (Config::getConfig()->getValue('enable_cache'))
		{
			// TODO: implement this
		}
		
		$db = Database::getConnection()->getPDO();
		
		try
		{
			$tmpplayers = $db->query(
			'SELECT r.player_id, r.points, p.name_pokerstars ' .
			'FROM ' .
			'(SELECT player_id, SUM(points) AS points, tournament_id ' .
			'FROM results ' .
			'WHERE player_id IS NOT NULL ' .
			'GROUP BY player_id) r ' .
			'JOIN tournaments t ON r.tournament_id=t.tournament_id ' .
			'JOIN players p ON r.player_id = p.player_id ' .
			'WHERE DATEDIFF(CURDATE(), t.tournament_date) <= 30*6 ' .
			'ORDER BY r.points DESC ' .
			'LIMIT 40');
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries:' . $e->getMessage());
		}
		
		$results = array();
		foreach($tmpplayers as $r)
		{
			$results[] = array('player_id' => $r->player_id,
								'name_pokerstars' => $r->name_pokerstars,
								'points' => $r->points
			);
		}
		
		return $results;
	}
	
	public function getGeneralStatistics()
	{
		if (Config::getConfig()->getValue('enable_cache'))
		{
			// TODO: implement this
		}
		
		$db = Database::getConnection()->getPDO();
		
		try
		{
			$tmpallpoints = $db->query(
			'SELECT p.initial_accumulated_points + p.initial_spent_points + r.points + b.bonus_value ' .
			'AS total_points ' .
			'FROM ' .
			'(SELECT SUM(initial_accumulated_points) AS initial_accumulated_points, ' .
			'SUM(initial_spent_points) AS initial_spent_points ' .
			'FROM players) p, ' .
			'(SELECT SUM(points) AS points FROM results) r, ' .
			'(SELECT SUM(bonus_value) AS bonus_value FROM bonus_points) b');
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries:' . $e->getMessage());
		}
		
		foreach($tmpallpoints as $r)
		{
			$results = array('total_points' => $r->total_points
				
			);
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