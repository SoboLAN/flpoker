<?php

namespace FileListPoker\Main\Cache;

use FileListPoker\Main\FLPokerException;

use Doctrine\Common\Cache\CacheProvider;

use PDO as PDO;
use PDOException as PDOException;

/**
 * Implements caching functionality using a database to store the cached objects.
 */
class PDOCache extends CacheProvider
{
    /**
     * @var PDO
     */
    private $db;
    
    /**
     * Constructor.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    
    protected function doContains($id)
    {
        try {
            $statement = $this->db->prepare('SELECT entry_time, lifetime FROM cache WHERE cache_key=?');
            $statement->execute(array($id));
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf('calling PDOCache::doContains failed for key %s: %s', $id, $e->getMessage()),
                FLPokerException::ERROR
            );
        }
        
        if ($statement->rowCount() == 0) {
            return false;
        } elseif ($statement->rowCount() > 1) {
            throw new FLPokerException(
                sprintf('calling PDOCache::doContains returned multiple rows for key %s: %s', $id, $e->getMessage()),
                FLPokerException::ERROR
            );
        }
        
        $row = $statement->fetch();
        
        //if cache entry is expired, delete it
        if (time() - $row['entry_time'] > $row['lifetime']) {
            $this->doDelete($id);
            return false;
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        try {
            $statement = $this->db->prepare('DELETE FROM cache WHERE cache_key=?');
            $statement->execute(array($id));
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf('calling PDOCache::doDelete failed with key %s: %s', $id, $e->getMessage()),
                FLPokerException::ERROR
            );
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        try {
            $statement = $this->db->prepare('SELECT value FROM cache WHERE cache_key=?');
            $statement->execute(array($id));
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf('calling PDOCache::doFetch failed with key %s: %s', $id, $e->getMessage()),
                FLPokerException::ERROR
            );
        }
        
        $row = $statement->fetch();
        
        return $row['value'];
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        try {
            $this->db->query('DELETE FROM cache WHERE entry_time + lifetime < UNIX_TIMESTAMP(NOW())');
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf('calling PDOCache::doFlush failed: %s', $e->getMessage()),
                FLPokerException::ERROR
            );
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        //this particular operation is performed within a transaction in order to minimize
        //the potential issues caused by concurrent cache writes of the same item
        $this->db->beginTransaction();
        
        $statement = $this->db->prepare(
            'INSERT INTO cache(cache_key, value, entry_time, lifetime) VALUES (?, ?, ?, ?)'
        );
        
        try {
            $statement->execute(array($id, $data, time(), $lifeTime));
            
            $this->db->commit();
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf(
                    'calling PDOCache::doSave failed with key %s, value %s, lifetime %s: %s',
                    $id,
                    $data,
                    $lifeTime,
                    $e->getMessage()
                ),
                FLPokerException::ERROR
            );
        }
        
        return true;
    }
}
