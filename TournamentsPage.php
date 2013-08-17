<?php
require_once 'DB.class.php';
require_once 'Config.class.php';

class TournamentsPage
{
	public function __construct()
	{
		
	}
	
	public function getContent()
	{		
		$db = Database::getConnection()->getPDO();

		try
		{
			$tmptournaments = $db->query ('SELECT tournament_id, YEAR(tournament_date) AS year, ' .
										'MONTH(tournament_date) AS month, DAYOFMONTH(tournament_date) AS day, ' .
										'tournament_type, participants ' .
										'FROM tournaments ' .
										'ORDER BY tournament_date DESC');
		}
		catch (PDOException $e)
		{
			die('There was a problem while performing database queries:' . $e->getMessage());
		}
		
		$tournaments = array();
		foreach ($tmptournaments as $tournament)
		{
			$tournaments[] = $tournament;
		}
		
		$final_result = array();
		
		foreach ($tournaments as $tournament)
		{
			$final_result[] = array('id' => $tournament->tournament_id,
									'day' => $tournament->day,
									'month' => $tournament->month,
									'year' => $tournament->year,
									'type' => $tournament->tournament_type,
									'participants' => $tournament->participants
			);
		}
		
		return $final_result;
	}
}