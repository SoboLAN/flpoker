<?php

require_once 'DB.class.php';
require_once 'CacheInterface.php';

class CacheDB implements CacheInterface
{
	private $DB;
	
	public function __construct()
	{
		$this->DB = Database::getConnection()->getPDO();
	}
	
	public function contains($key, $lifetime)
	{
		$result = $this->DB->query ("SELECT value, entry_time FROM cache WHERE cache_key='$key'");
		if ($result->rowCount () == 0)
		{
			return false;
		}
		elseif ($result->rowCount() > 1)
		{
			die('There was an error while displaying the page.');
		}
		
		foreach ($result as $row)
		{
			$time = $row->entry_time;
		}
		
		if (time() - $time > $lifetime)
		{
			$this->flush($key);
			return false;
		}
		
		return true;
	}

	public function flush($key)
	{
		$result = $this->DB->exec ("DELETE FROM cache WHERE cache_key='$key'");
	}

	public function getContent($key)
	{
		$result = $this->DB->query ("SELECT value FROM cache WHERE cache_key='$key'");
		
		foreach ($result as $row)
		{
			$value = $row->value;
			return $value;
		}
	}

	public function save($key, $value, $lifetime)
	{
		$this->DB->beginTransaction();
		
		$time = time () + $lifetime;
		
		$statement = $this->DB->prepare('INSERT INTO cache(cache_key, value, entry_time) VALUES (?, ?, ?)');
		
		try{
		
			$statement->execute (array ($key, $value, $time));
		}
		catch (PDOException $e)
		{
			var_dump($e->getMessage());
		}
		if ($statement->rowCount () !== 1)
		{
			die ('There was an error while displaying the page');
		}
		
		$this->DB->commit ();
	}	
}