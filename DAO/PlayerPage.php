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
}