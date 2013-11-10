<?php

namespace FileListPoker\Main;

/**
 * Interface for classes that implement caching functionality.
 */
interface CacheInterface
{
    /**
     * Checks if an object with the specified key exists. Returns true if it does, false otherwise.
     * However, if the object exists and it's dead, then this function will delete it and
     * return false.
     * @param string $key the key of the object to be checked
     * @return boolean true if the object exists, false otherwise.
     */
    public function contains($key);
    
    /**
     * Returns the object identified by the specified key.
     * @param string $key the key of the object to be retrieved.
     * @return string the 
     */
    public function getContent($key);
    
    /**
     * Saves an object to the cache using the specified key and lifetime.
     * @param string $key key of the new object.
     * @param string $value the object to be saved.
     * @param int $lifetime life time of the new object expressed in seconds.
     */
    public function save($key, $value, $lifetime);
    
    /**
     * This method will delete the object identified by the specified key.
     * @param string $key the key of the object to be deleted.
     */
    public function flush($key);
}
