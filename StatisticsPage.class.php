<?php
require_once 'DB.class.php';
require_once 'Config.class.php';

class StatisticsPage
{
	public function __construct()
	{
		
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
}