<?php

namespace FileListPoker\Main;

/**
 * Handles the construction of an object capable of performing database operations.
 */
class Database
{
    private static $configPath = 'config/db.config.json';
    
    //the database connection object
    private static $connection;

    /**
     * Returns a database connection object. Always the same one.
     * @return object a database connection object.
     */
    public static function getConnection ()
    {
        if (is_null(self::$connection)) {
            self::buildConnection();
        }

        return self::$connection;
    }

    private static function buildConnection()
    {
        //get database connection options
        $dbConfig = json_decode(file_get_contents(self::$configPath), true);
        
        //versions of MySQL older than this don't support prepared statements.
        //they have to be simulated by PDO
        $emulate_prepares_below_version = '5.1.17';

        $dsndefaults = array_fill_keys(array('host', 'port', 'unix_socket', 'dbname', 'charset'), null);
        $dsnarr = array_intersect_key($dbConfig, $dsndefaults);
        $dsnarr += $dsndefaults;

        // connection options I like
        $options = array (
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        );

        // connection charset handling for old php versions
        if ($dsnarr['charset'] and version_compare(PHP_VERSION, '5.3.6', '<')) {
            $options[\PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES '.$dsnarr['charset'];
        }
        $dsnpairs = array();
        foreach ($dsnarr as $k => $v) {
            if ($v===null) {
                continue;
            }
            
            $dsnpairs[] = "{$k}={$v}";
        }

        try {
            $dsn = 'mysql:' . implode(';', $dsnpairs);
            self::$connection = new \PDO($dsn, $dbConfig['user'], $dbConfig['pass'], $options);

            // Set prepared statement emulation depending on server version
            $serverversion = self::$connection->getAttribute(\PDO::ATTR_SERVER_VERSION);
            $emulate_prepares = (version_compare($serverversion, $emulate_prepares_below_version, '<'));
            self::$connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, $emulate_prepares);
        } catch (\PDOException $e) {
            die ($e->getMessage());
        }
    }
}
