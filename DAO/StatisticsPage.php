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
	
	public function getMostActivePlayers()
	{
		/*
		 	query:
			SELECT count( points ) AS freq, player_id
		   FROM results
		   WHERE player_id IS NOT NULL
		   GROUP BY player_id
		   ORDER BY freq DESC
		 */
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