<?php

namespace FileListPoker\Content;

use PDOException as PDOException;

use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Config;
use FileListPoker\Main\CacheDB;

/**
 * This class contains functions that will return information about the Players of the Month.
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class PlayersMonthContent
{
    private $cache;
    
    public function __construct()
    {
        //set cache field with an apropiate cache instance (based on type), but only
        //if caching is enabled
        if (Config::getValue('enable_cache')) {
            $cacheType = Config::getValue('cache_type');
        
            if ($cacheType == 'db') {
                $this->cache = new CacheDB();
            }
        }
    }
    
    /**
     * Returns an associative array of information about all the players of the month.
     * @return array of information about the players of the month. Will contain:
     * <ul>
     * <li>player's ID</li>
     * <li>FileList ID</li>
     * <li>FileList Name</li>
     * <li>PokerStars Name</li>
     * <li>month of obtaining the distinction</li>
     * <li>year of obtaining the distinction</li>
     * <li>points obtained in that month/year</li>
     * <li>account type</li>
     * </ul>
     */
    public function getPlayersOfTheMonth()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players_of_the_month');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->getContent($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection();

        try {
            $players = $db->query(
                'SELECT m.player_id, m.award_month, m.award_year, ' .
                'p.id_filelist, p.name_filelist, p.name_pokerstars, p.member_type, ' .
                    '(SELECT SUM(r.points) ' .
                    'FROM results r ' .
                    'JOIN tournaments t ON r.tournament_id=t.tournament_id ' .
                    'WHERE MONTH(t.tournament_date)=m.award_month ' .
                    'AND YEAR(t.tournament_date)=m.award_year ' .
                    'AND r.player_id=m.player_id) AS points ' .
                'FROM players_of_the_month m ' .
                'JOIN players p ON m.player_id = p.player_id ' .
                'ORDER BY m.award_year DESC, m.award_month DESC'
            );
        } catch (PDOException $e) {
            $message = "calling PlayersMonthContent::getPlayersOfTheMonth failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players_of_the_month');
            
            $lifetime = Config::getValue('cache_lifetime_players_of_the_month');
            
            $this->cache->save($key, json_encode($players), $lifetime);
        }
        
        return $players;
    }
}
