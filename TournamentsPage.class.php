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
			$tmptournaments = $db->query ('SELECT tournament_id, tournament_date, ' .
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
			$final_result[] = array('tournament_id' => $tournament->tournament_id,
									'tournament_date' => $tournament->tournament_date,
									'tournament_type' => $tournament->tournament_type,
									'participants' => $tournament->participants
			);
		}
		
		return $final_result;
	}
}