<?php
require_once '/../DB.class.php';
require_once '/../Config.class.php';

class PlayerPage
{
	public function __construct()
	{
	
	}
	
	public function getGeneral($pid)
	{
		if (Config::getConfig()->getValue('enable_cache'))
		{
			// TODO: implement this
		}
		
		$db = Database::getConnection()->getPDO();
		
		$pidesc = $db->quote($pid);
		
		try
		{
			$playerInfo = $db->query ('SELECT id_filelist, name_pokerstars, name_filelist, ' .
								'initial_accumulated_points, initial_spent_points, ' .
								'MONTH(join_date) AS month, ' .
								'DAYOFMONTH(join_date) AS day, YEAR(join_date) AS year, ' .
								'member_type ' .
								'FROM players ' .
								'WHERE player_id=' . $pidesc . ' ' .
								'ORDER BY player_id ASC');

			$tmpresults = $db->query('SELECT SUM(points) AS points ' .
								'FROM results ' .
								'WHERE player_id=' . $pid);

			$tmpbonuses = $db->query('SELECT SUM(bonus_value) AS bonus_value ' .
								'FROM bonus_points ' .
								'WHERE player_id=' . $pidesc);
			
			$tmpprizes = $db->query('SELECT SUM(cost) AS cost ' .
								'FROM prizes ' .
								'WHERE player_id=' . $pidesc);
			
			$tmpfinaltables = $db->query('SELECT COUNT(*) AS final_tables ' .
								'FROM results ' .
								'WHERE position <= 9 AND player_id=' . $pidesc);
			
			$tmpmedals = $db->query('SELECT * FROM (' .
									'SELECT COUNT(*) AS gold_medals ' .
									'FROM results ' .
									'WHERE position=1 ' .
									'AND player_id=' . $pidesc. ') AS gold_medals, ' .
									'(SELECT COUNT(*) AS silver_medals ' .
									'FROM results ' .
									'WHERE position =2 ' .
									'AND player_id=' . $pidesc. ') AS silver_medals, ' .
									'(SELECT COUNT(*) AS bronze_medals ' .
									'FROM results ' .
									'WHERE position=3 ' .
									'AND player_id=' . $pidesc. ') AS bronze_medals');
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries: ' . $e->getMessage());
		}
		
		$results = 0;
		$bonuses = 0;
		$prizes = 0;
		$final_tables = 0;
		$gold_medals = $silver_medals = $bronze_medals = 0;
		foreach ($tmpresults as $r)
		{
			$results = $r->points;
		}
		foreach ($tmpbonuses as $b)
		{
			$bonuses = $b->bonus_value;
		}
		foreach ($tmpprizes as $p)
		{
			$prizes = $p->cost;
		}
		foreach ($tmpfinaltables as $f)
		{
			$final_tables = $f->final_tables;
		}
		foreach ($tmpmedals as $medals)
		{
			$gold_medals = $medals->gold_medals;
			$silver_medals = $medals->silver_medals;
			$bronze_medals = $medals->bronze_medals;
		}

		foreach ($playerInfo as $pInfo)
		{
			$points = $pInfo->initial_accumulated_points + $results + $bonuses - $prizes;
			$pointsAllTime = $pInfo->initial_accumulated_points + $pInfo->initial_spent_points + 
							$results + $bonuses;
					
			$final_result = array ('id_filelist' => $pInfo->id_filelist,
									'name_pokerstars' => $pInfo->name_pokerstars,
									'name_filelist' => $pInfo->name_filelist,
									'month' => $pInfo->month,
									'day' => $pInfo->day,
									'year' => $pInfo->year,
									'member_type' => $pInfo->member_type,
									'points' => $points,
									'final_tables' => $final_tables,
									'gold_medals' => $gold_medals,
									'silver_medals' => $silver_medals,
									'bronze_medals' => $bronze_medals,
									'points_all_time' => $pointsAllTime
			);
		}
		
		return $final_result;
	}
	
	public function getTournamentHistory($pid)
	{
		if (Config::getConfig()->getValue('enable_cache'))
		{
			// TODO: implement this
		}
		
		$db = Database::getConnection()->getPDO();
		
		$pidesc = $db->quote($pid);
		
		try
		{
			$history = $db->query ('SELECT t.tournament_id, DAYOFMONTH(t.tournament_date) AS day, ' .
									'MONTH(t.tournament_date) AS month, YEAR(t.tournament_date) AS year, ' .
									'r.points, r.position ' .
									'FROM tournaments t ' .
									'JOIN results r ON t.tournament_id=r.tournament_id ' .
									'WHERE r.player_id=' . $pidesc .
									'ORDER BY t.tournament_date DESC');
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries: ' . $e->getMessage());
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
		
		$pidesc = $db->quote($pid);
		
		try
		{
			$bonuses = $db->query ('SELECT bonus_value, tournament_id, bonus_description, ' .
									'DAYOFMONTH(bonus_date) AS day, MONTH(bonus_date) AS month , ' .
									'YEAR(bonus_date) AS year, ' .
									'UNIX_TIMESTAMP(bonus_date) AS stamp ' .
									'FROM bonus_points ' .
									'WHERE player_id=' . $pidesc . ' ' .
									'ORDER BY stamp ASC');
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries: ' . $e->getMessage());
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
		
		$pidesc = $db->quote($pid);
		
		try
		{
			$prizes = $db->query ('SELECT prize, cost, ' .
									'DAYOFMONTH(date_bought) AS day, ' .
									'MONTH(date_bought) AS month , ' .
									'YEAR(date_bought) AS year ' .
									'FROM prizes ' .
									'WHERE player_id=' . $pidesc);
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries: ' . $e->getMessage());
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