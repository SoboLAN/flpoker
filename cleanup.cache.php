<?php

require_once 'autoload.php';
use FileListPoker\Main\Database;

$db = Database::getConnection();

try
{
	//delete expired cache entries
	$result = $db->query ('DELETE FROM cache WHERE entry_time + lifetime < UNIX_TIMESTAMP(NOW())');
}
catch (\PDOException $e)
{
	die('There was a problem while performing database queries');
}