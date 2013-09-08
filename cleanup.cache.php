<?php

require_once 'DB.class.php';

$db = Database::getConnection()->getPDO();

try
{
	//delete cache entries older than 4 days
	$age = 2 * 24 * 3600;
	$result = $db->prepare ('DELETE FROM cache WHERE UNIX_TIMESTAMP(NOW()) - entry_time > ?');
	$result->bindParam (1, $age, PDO::PARAM_INT);
	$result->execute ();
}
catch (PDOException $e)
{
	die('There was a problem while performing database queries');
}