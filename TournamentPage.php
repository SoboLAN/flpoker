<?php
require_once 'DB.class.php';
require_once 'Config.class.php';

class TournamentPage
{
	public function __construct()
	{
		
	}
	
	public function getTournamentDetails($tid)
	{		
		$db = Database::getConnection()->getPDO();
		
		//just beeing extra-safe
		$tidesc = $db->quote($tid);

		try
		{
			$tmptournament = $db->query ('SELECT tournament_id, YEAR(tournament_date) AS year, ' .
										'MONTH(tournament_date) AS month, DAYOFMONTH(tournament_date) AS day, ' .
										'tournament_type, participants ' .
										'FROM tournaments ' .
										'WHERE tournament_id=' . $tidesc);
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries:' . $e->getMessage());
		}
		
		$final_result = array();
		foreach ($tmptournament as $t)
		{
			$final_result = array('id' => $t->tournament_id,
								'day' => $t->day,
								'month' => $t->month,
								'year' => $t->year,
								'type' => $t->tournament_type,
								'participants' => $t->participants
			);
		}

		return $final_result;
	}
	
	public function getTournamentResults($tid)
	{		
		$db = Database::getConnection()->getPDO();
		
		//just beeing extra-safe
		$tidesc = $db->quote($tid);

		try
		{
			$results = $db->query ('SELECT r.player_id, p.name_pokerstars, r.points, r.position ' .
									'FROM results r ' .
									'LEFT JOIN players p ON r.player_id = p.player_id ' .
									'WHERE r.tournament_id=' . $tidesc .
									'ORDER BY r.position ASC');
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries:' . $e->getMessage());
		}
		
		$final_result = array();
		foreach($results as $result)
		{
			$final_result[] = array('player_id' => $result->player_id,
									'name_pokerstars' => $result->name_pokerstars,
									'points' => $result->points,
									'position' => $result->position
			);
		}
		
		return $final_result;
	}
}