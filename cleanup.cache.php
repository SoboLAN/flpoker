<?php

require_once 'autoload.php';
use FileListPoker\Main\Database;

$db = Database::getConnection()->getPDO();

try
{
	//delete expired cache entries
	$result = $db->prepare ('DELETE FROM cache WHERE entry_time + lifetime < ?');
	$result->bindParam (1, time(), \PDO::PARAM_INT);
	$result->execute ();
}
catch (\PDOException $e)
{
	die('There was a problem while performing database queries');
}