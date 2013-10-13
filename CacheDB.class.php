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
	
	public function contains($key)
	{
		try
		{
			$statement = $this->DB->prepare('SELECT entry_time, lifetime FROM cache WHERE cache_key=?');
			$statement->execute(array($key));
		}
		catch (PDOException $e)
		{
			die ('There was an error while displaying the page1');
		}
		
		if ($statement->rowCount () == 0)
		{
			return false;
		}
		elseif ($statement->rowCount() > 1)
		{
			die('There was an error while displaying the page2');
		}
		
		$row = $statement->fetch(PDO::FETCH_OBJ);
		$entryTime = $row->entry_time;
		$lifeTime = $row->lifetime;
		
		if (time() - $entryTime > $lifeTime)
		{
			$this->flush($key);
			return false;
		}
		
		return true;
	}

	public function flush($key)
	{
		try
		{
			$statement = $this->DB->prepare ('DELETE FROM cache WHERE cache_key=?');
			$statement->execute (array ($key));
		}
		catch (PDOException $e)
		{
			die ('There was an error while displaying the page3');
		}
	}

	public function getContent($key)
	{
		try
		{
			$statement = $this->DB->prepare ('SELECT value FROM cache WHERE cache_key=?');
			$statement->execute (array ($key));
		}
		catch (PDOException $e)
		{
			die ('There was an error while displaying the page4');
		}
		
		$row = $statement->fetch (PDO::FETCH_OBJ);
		
		return $row->value;
	}

	public function save($key, $value, $lifetime)
	{
		$this->DB->beginTransaction();
		
		$statement = $this->DB->prepare('INSERT INTO cache(cache_key, value, entry_time, lifetime) VALUES (?, ?, ?, ?)');
		
		try
		{
			$statement->execute (array ($key, $value, time (), $lifetime));
		}
		catch (PDOException $e)
		{
			die ('There was an error while displaying the page5');
		}
		
		if ($statement->rowCount () !== 1)
		{
			die ('There was an error while displaying the page6');
		}
		
		$this->DB->commit ();
	}	
}