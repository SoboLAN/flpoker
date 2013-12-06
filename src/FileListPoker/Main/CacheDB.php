<?php

namespace FileListPoker\Main;

use FileListPoker\Main\Database;
use FileListPoker\Main\CacheInterface;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;

/**
 * Implements caching functionality using a database to store the cached objects.
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class CacheDB implements CacheInterface
{
    //a database connection object
    private $DB;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->DB = Database::getConnection();
    }
    
    /**
     * Checks if an object with the specified key exists. Returns true if it does, false otherwise.
     * However, if the object exists and it's dead, then this function will delete it and
     * return false.
     * @param string $key the key of the object to be checked
     * @return boolean true if the object exists, false otherwise.
     */
    public function contains ($key)
    {
        try {
            $statement = $this->DB->prepare('SELECT entry_time, lifetime FROM cache WHERE cache_key=?');
            $statement->execute(array($key));
        } catch (\PDOException $e) {
            $message = "calling CacheDB::contains failed for key $key";
            Logger::log("$message: " . $e->getMessage());
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        if ($statement->rowCount() == 0) {
            return false;
        } elseif ($statement->rowCount() > 1) {
            $message = "calling CacheDB::contains returned multiple rows for key $key";
            Logger::log($message);
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $row = $statement->fetch(\PDO::FETCH_OBJ);
        $entryTime = $row->entry_time;
        $lifeTime = $row->lifetime;
        
        //if cache entry is expired, delete it
        if (time() - $entryTime > $lifeTime) {
            $this->flush($key);
            return false;
        }
        
        return true;
    }

    /**
     * This method will delete the object identified by the specified key.
     * @param string $key the key of the object to be deleted.
     */
    public function flush($key)
    {
        try {
            $statement = $this->DB->prepare('DELETE FROM cache WHERE cache_key=?');
            $statement->execute(array($key));
        } catch (\PDOException $e) {
            $message = "calling CacheDB::flush failed with key $key";
            Logger::log("$message: " . $e->getMessage());
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
    }

    /**
     * Returns the object identified by the specified key.
     * @param string $key the key of the object to be retrieved.
     * @return string the 
     */
    public function getContent($key)
    {
        try {
            $statement = $this->DB->prepare('SELECT value FROM cache WHERE cache_key=?');
            $statement->execute(array($key));
        } catch (\PDOException $e) {
            $message = "calling CacheDB::getContent failed with key $key";
            Logger::log("$message: " . $e->getMessage());
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $row = $statement->fetch(\PDO::FETCH_OBJ);
        
        return $row->value;
    }

    /**
     * Saves an object to the cache using the specified key and lifetime.
     * @param string $key key of the new object.
     * @param string $value the object to be saved.
     * @param int $lifetime life time of the new object expressed in seconds.
     */
    public function save($key, $value, $lifetime)
    {
        //this particular operation is performed within a transaction in order to minimize
        //the potential issues caused by concurrent cache writes of the same item
        $this->DB->beginTransaction();
        
        $statement = $this->DB->prepare(
            'INSERT INTO cache(cache_key, value, entry_time, lifetime) VALUES (?, ?, ?, ?)'
        );
        
        try {
            $statement->execute(array($key, $value, time(), $lifetime));
            
            $this->DB->commit();
        } catch (\PDOException $e) {
            $message = "calling CacheDB::save failed with key $key, $value value, lifetime $lifetime";
            Logger::log("$message: " . $e->getMessage());
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
    }
}
