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
		
		try
		{
			$tournamentSt = $db->prepare ('SELECT tournament_id, YEAR(tournament_date) AS year, ' .
										'MONTH(tournament_date) AS month, DAYOFMONTH(tournament_date) AS day, ' .
										'tournament_type, participants ' .
										'FROM tournaments ' .
										'WHERE tournament_id=?');
			
			$tournamentSt->bindParam (1, $tid, PDO::PARAM_INT);
			$tournamentSt->execute ();
			$tournament = $tournamentSt->rowCount () == 0 ? false : $tournamentSt->fetch (PDO::FETCH_OBJ);
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries');
		}
		
		if (! $tournament)
		{
			return array ();
		}
		
		return array('id' => $tournament->tournament_id,
					'day' => $tournament->day,
					'month' => $tournament->month,
					'year' => $tournament->year,
					'type' => $tournament->tournament_type,
					'participants' => $tournament->participants
		);
	}
	
	public function getTournamentResults($tid)
	{		
		$db = Database::getConnection()->getPDO();
		
		try
		{
			$resultsSt = $db->prepare ('SELECT r.player_id, p.name_pokerstars, r.points, r.position ' .
									'FROM results r ' .
									'LEFT JOIN players p ON r.player_id = p.player_id ' .
									'WHERE r.tournament_id=? ' .
									'ORDER BY r.position ASC');
			
			$resultsSt->bindParam (1, $tid, PDO::PARAM_INT);
			$resultsSt->execute ();
			
			$results = array();
			while ($row = $resultsSt->fetch (PDO::FETCH_OBJ))
			{
				$results[] = $row;
			}
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries');
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