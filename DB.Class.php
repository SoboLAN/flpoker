<?php
	require_once 'config/db.config.php';
	
	class Database
	{
		private static $__instance;

		private $connection;
		
		public static function getConnection()
		{
			if (is_null (self::$__instance))
			{
				self::$__instance = new Database();
			}
			
			return self::$__instance;
		}
		
		private __construct()
		{
			$emulate_prepares_below_version = '5.1.17';

			$dsndefaults = array_fill_keys(array('host', 'port', 'unix_socket', 'dbname', 'charset'), null);
			$dsnarr = array_intersect_key($dbConfig, $dsndefaults);
			$dsnarr += $dsndefaults;

			// connection options I like
			$options = array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			);

			// connection charset handling for old php versions
			if ($dsnarr['charset'] and version_compare(PHP_VERSION, '5.3.6', '<'))
			{
				$options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES '.$dsnarr['charset'];
			}
			$dsnpairs = array();
			foreach ($dsnarr as $k => $v)
			{
				if ($v===null) continue;
				$dsnpairs[] = "{$k}={$v}";
			}

			try
			{
				$dsn = 'mysql:'.implode(';', $dsnpairs);
				$this->connection = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], $options);

				// Set prepared statement emulation depending on server version
				$serverversion = $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
				$emulate_prepares = (version_compare($serverversion, $emulate_prepares_below_version, '<'));
				$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, $emulate_prepares);
			}
			catch (PDOException $e)
			{
				die ($e->getMessage());
			}
		}
	}