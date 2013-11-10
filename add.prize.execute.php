<?php

if (! isset($_POST['flpokerpassword']) or $_POST['flpokerpassword'] !== 'myflpass1234do')
{
	die ('nice try.');
}

if (! isset ($_POST['player']) or
	! isset ($_POST['prize']) or
	! isset ($_POST['cost']) or
	! isset ($_POST['purchasedate']))
{
	die ('Some data is missing.');
}

require_once 'autoload.php';
use FileListPoker\Main\Database;

$db = Database::getConnection();

try
{
	$getIdStatement = $db->prepare ('SELECT player_id FROM players WHERE name_filelist=?');
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

	$insertSt = $db->prepare ('INSERT INTO prizes (prize_id, player_id, prize, cost, date_bought, prize_type) ' .
							'VALUES ' .
							'(NULL, ?, ?, ?, ?, \'new\')');

	$insertSt->bindParam (1, $pid, \PDO::PARAM_INT);
	$insertSt->bindParam (2, $_POST['prize'], \PDO::PARAM_STR);
	$insertSt->bindParam (3, $_POST['cost'], \PDO::PARAM_INT);
	$insertSt->bindParam (4, $_POST['purchasedate'], \PDO::PARAM_STR);

	$insertSt->execute ();
	$rows = $insertSt->rowCount ();
}
catch (\PDOException $e)
{
	die ('There was an error while executing the script');
}

echo "Added $rows prize(s)";