<?php

if (! isset($_POST['flpokerpassword']) or $_POST['flpokerpassword'] !== 'myflpass1234do')
{
	die ('nice try.');
}

if (! isset ($_POST['player']) or
	! isset ($_POST['tid']) or
	! isset ($_POST['bonusvalue']) or
	! isset ($_POST['bonusdate']) or
	! isset ($_POST['bonusdesc']))
{
	die ('Some data is missing.');
}

require_once 'autoload.php';
use FileListPoker\Main\Database;

$db = Database::getConnection();

try
{
	$getIdStatement = $db->prepare ('SELECT player_id FROM players WHERE name_pokerstars=?');
	$getIdStatement->bindParam (1, $_POST['player'], \PDO::PARAM_STR);
	$getIdStatement->execute ();

	if ($getIdStatement->rowCount () !== 1)
	{
		die('player does not exist');
	}
	else
	{
		$pid = $getIdStatement->fetch (\PDO::FETCH_OBJ)->player_id;
	}

	$insertSt = $db->prepare ('INSERT INTO bonus_points (bonus_id, player_id, bonus_value, tournament_id, bonus_description, bonus_date) ' .
							'VALUES ' .
							'(NULL, ?, ?, ?, ?, ?)');

	$insertSt->bindParam (1, $pid, \PDO::PARAM_INT);
	$insertSt->bindParam (2, $_POST['bonusvalue'], \PDO::PARAM_INT);
	$insertSt->bindParam (3, $_POST['tid'], \PDO::PARAM_INT);
	$insertSt->bindParam (4, $_POST['bonusdesc'], \PDO::PARAM_STR);
	$insertSt->bindParam (5, $_POST['bonusdate'], \PDO::PARAM_STR);

	$insertSt->execute ();
	$rows = $insertSt->rowCount ();
}
catch (\PDOException $e)
{
	die ('There was an error while executing the script: ' . $e->getMessage());
}

echo "Added $rows bonus(es)";