<?php
require_once 'DB.class.php';
require_once 'Config.class.php';
require_once 'CacheDB.class.php';
require_once 'CacheFile.class.php';

class PlayerPage
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
	
	public function getGeneral($pid)
	{
		if (! is_null ($this->cache))
		{
			$key = Config::getConfig()->getValue('cache_key_player_general') . $pid;
			$lifetime = Config::getConfig()->getValue('cache_lifetime_player_general');
			if ($this->cache->contains ($key, $lifetime))
			{
				$content = json_decode ($this->cache->getContent($key), true);
				
				return $content;
			}
		}
		
		$db = Database::getConnection()->getPDO();

		try
		{
			$playerInfoSt = $db->prepare ('SELECT id_filelist, name_pokerstars, name_filelist, ' .
								'initial_accumulated_points, initial_spent_points, ' .
								'MONTH(join_date) AS month, ' .
								'DAYOFMONTH(join_date) AS day, YEAR(join_date) AS year, ' .
								'member_type ' .
								'FROM players ' .
								'WHERE player_id=? ' .
								'ORDER BY player_id ASC');

			$resultsSt = $db->prepare('SELECT SUM(points) AS points ' .
								'FROM results ' .
								'WHERE player_id=?');

			$bonusesSt = $db->prepare('SELECT SUM(bonus_value) AS bonus_value ' .
								'FROM bonus_points ' .
								'WHERE player_id=?');

			$prizesSt = $db->prepare('SELECT SUM(cost) AS cost ' .
								'FROM prizes ' .
								'WHERE player_id=?');

			$finalTablesSt = $db->prepare('SELECT COUNT(*) AS final_tables ' .
								'FROM results ' .
								'WHERE position <= 9 AND player_id=?');

			$medalsSt = $db->prepare('SELECT * FROM (' .
									'SELECT COUNT(*) AS gold_medals ' .
									'FROM results ' .
									'WHERE position=1 ' .
									'AND player_id=?) AS gold_medals, ' .
									'(SELECT COUNT(*) AS silver_medals ' .
									'FROM results ' .
									'WHERE position=2 ' .
									'AND player_id=?) AS silver_medals, ' .
									'(SELECT COUNT(*) AS bronze_medals ' .
									'FROM results ' .
									'WHERE position=3 ' .
									'AND player_id=?) AS bronze_medals');
			
			$playerInfoSt->bindParam (1, $pid, PDO::PARAM_INT);
			$playerInfoSt->execute ();
			$playerInfo = $playerInfoSt->rowCount () == 0 ? false : $playerInfoSt->fetch (PDO::FETCH_OBJ);
			
			$resultsSt->bindParam (1, $pid, PDO::PARAM_INT);
			$resultsSt->execute ();
			$results = $resultsSt->fetch (PDO::FETCH_OBJ)->points;
			
			$bonusesSt->bindParam (1, $pid, PDO::PARAM_INT);
			$bonusesSt->execute ();
			$bonuses = $bonusesSt->fetch (PDO::FETCH_OBJ)->bonus_value;
			
			$prizesSt->bindParam (1, $pid, PDO::PARAM_INT);
			$prizesSt->execute ();
			$prizes = $prizesSt->fetch (PDO::FETCH_OBJ)->cost;
			
			$finalTablesSt->bindParam (1, $pid, PDO::PARAM_INT);
			$finalTablesSt->execute ();
			$finalTables = $finalTablesSt->fetch (PDO::FETCH_OBJ)->final_tables;
			
			$medalsSt->bindParam (1, $pid, PDO::PARAM_INT);
			$medalsSt->bindParam (2, $pid, PDO::PARAM_INT);
			$medalsSt->bindParam (3, $pid, PDO::PARAM_INT);
			$medalsSt->execute ();
			$medalsObj = $medalsSt->fetch (PDO::FETCH_OBJ);
			$gold_medals = $medalsObj->gold_medals;
			$silver_medals = $medalsObj->silver_medals;
			$bronze_medals = $medalsObj->bronze_medals;
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries');
		}

		if(! $playerInfo)
		{
			return array();
		}

		$points = $playerInfo->initial_accumulated_points + $results + $bonuses - $prizes;
		$pointsAllTime = $playerInfo->initial_accumulated_points + $playerInfo->initial_spent_points + 
							$results + $bonuses;
					
		$final_result = array ('id_filelist' => $playerInfo->id_filelist,
								'name_pokerstars' => $playerInfo->name_pokerstars,
								'name_filelist' => $playerInfo->name_filelist,
								'month' => $playerInfo->month,
								'day' => $playerInfo->day,
								'year' => $playerInfo->year,
								'member_type' => $playerInfo->member_type,
								'points' => $points,
								'final_tables' => $finalTables,
								'gold_medals' => $gold_medals,
								'silver_medals' => $silver_medals,
								'bronze_medals' => $bronze_medals,
								'points_all_time' => $pointsAllTime
		);
		
		if (! is_null ($this->cache))
		{
			$key = Config::getConfig()->getValue('cache_key_player_general') . $pid;
			
			$this->cache->save($key, json_encode($final_result));
		}
		
		return $final_result;
	}
	
	public function getTournamentHistory($pid)
	{
		$db = Database::getConnection()->getPDO();
		
		try
		{
			$historySt = $db->prepare ('SELECT t.tournament_id, DAYOFMONTH(t.tournament_date) AS day, ' .
									'MONTH(t.tournament_date) AS month, YEAR(t.tournament_date) AS year, ' .
									'r.points, r.position ' .
									'FROM tournaments t ' .
									'JOIN results r ON t.tournament_id=r.tournament_id ' .
									'WHERE r.player_id=? ' .
									'ORDER BY t.tournament_date DESC');
			
			$historySt->bindParam (1, $pid, PDO::PARAM_INT);
			$historySt->execute ();
			
			$history = array ();
			while ($row = $historySt->fetch (PDO::FETCH_OBJ))
			{
				$history[] = $row;
			}
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries');
		}
		
		$final_result = array();
		
		foreach ($history as $tournament)
		{
			$final_result[] = array ('tournament_id' => $tournament->tournament_id,
									'day' => $tournament->day,
									'month' => $tournament->month,
									'year' => $tournament->year,
									'points' => $tournament->points,
									'position' => $tournament->position
							
			);
		}
		
		return $final_result;
	}
	
	public function getBonuses($pid)
	{		
		$db = Database::getConnection()->getPDO();
		
		try
		{
			$bonusesSt = $db->prepare ('SELECT bonus_value, tournament_id, bonus_description, ' .
									'DAYOFMONTH(bonus_date) AS day, MONTH(bonus_date) AS month , ' .
									'YEAR(bonus_date) AS year, ' .
									'UNIX_TIMESTAMP(bonus_date) AS stamp ' .
									'FROM bonus_points ' .
									'WHERE player_id=? ' .
									'ORDER BY stamp ASC');
			
			$bonusesSt->bindParam (1, $pid, PDO::PARAM_INT);
			$bonusesSt->execute ();
			
			$bonuses = array ();
			while ($row = $bonusesSt->fetch (PDO::FETCH_OBJ))
			{
				$bonuses[] = $row;
			}
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries');
		}
		
		$final_result = array();
		
		foreach ($bonuses as $bonus)
		{
			$final_result[] = array ('tournament_id' => $bonus->tournament_id,
									'day' => $bonus->day,
									'month' => $bonus->month,
									'year' => $bonus->year,
									'bonus_value' => $bonus->bonus_value,
									'description' => $bonus->bonus_description
			);
		}
		
		return $final_result;
	}
	
	public function getPrizes($pid)
	{		
		$db = Database::getConnection()->getPDO();

		try
		{
			$prizesSt = $db->prepare ('SELECT prize, cost, ' .
									'DAYOFMONTH(date_bought) AS day, ' .
									'MONTH(date_bought) AS month , ' .
									'YEAR(date_bought) AS year ' .
									'FROM prizes ' .
									'WHERE player_id=?');
			
			$prizesSt->bindParam (1, $pid, PDO::PARAM_INT);
			$prizesSt->execute ();
			
			$prizes = array ();
			while ($row = $prizesSt->fetch (PDO::FETCH_OBJ))
			{
				$prizes[] = $row;
			}
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries');
		}
		
		$final_result = array();
		
		foreach ($prizes as $prize)
		{
			$final_result[] = array ('prize' => $prize->prize,
									'day' => $prize->day,
									'month' => $prize->month,
									'year' => $prize->year,
									'cost' => $prize->cost
							
			);
		}
		
		return $final_result;
	}
}