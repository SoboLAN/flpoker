<?php

namespace FileListPoker\Content;

use PDOException as PDOException;

use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Config;
use FileListPoker\Main\Cache\CacheFactory;

use Doctrine\Common\Cache\CacheProvider;

/**
 * This class contains functions that will return information about the Players of the Month.
 */
class PlayersMonthContent
{
    /**
     * @var CacheProvider
     */
    private $cache;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cache = CacheFactory::getCacheInstance();
    }

    /**
     * Returns an associative array of information about all the players of the month.
     * @return array of information about the players of the month.
     */
    public function getPlayersOfTheMonth()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players_of_the_month');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->fetch($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection();

        try {
            $playersSt = $db->query(
                'SELECT m.player_id, m.award_month, m.award_year, m.points, ' .
                'p.id_filelist, p.name_filelist, p.name_pokerstars, p.member_type ' .
                'FROM players_of_the_month m ' .
                'JOIN players p ON m.player_id = p.player_id ' .
                'ORDER BY m.award_year DESC, m.award_month DESC'
            );
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf('calling PlayersMonthContent::getPlayersOfTheMonth failed: %s', $e->getMessage()),
                FLPokerException::ERROR
            );
        }
        
        $players = $playersSt->fetchAll();
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players_of_the_month');
            
            $lifetime = Config::getValue('cache_lifetime_players_of_the_month');
            
            $this->cache->save($key, json_encode($players), $lifetime);
        }
        
        return $players;
    }
}
