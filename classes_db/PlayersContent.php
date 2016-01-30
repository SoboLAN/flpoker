<?php

namespace FileListPoker\Content;

use FileListPoker\Main\Database;
use FileListPoker\Main\Config;
use FileListPoker\Main\Cache\CacheFactory;
use FileListPoker\Main\FLPokerException;

use Doctrine\Common\Cache\CacheProvider;

use PDOException as PDOException;

/**
 * This class contains functions that will return information about all the players.
 */
class PlayersContent
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
     * Returns an associative array of information about the players Not all players will be
     * returned, since the pagination parameters will probably eliminate most of them from
     * the result set.
     * @param int $page which page of players to return.
     * @param int $perPage how many players per page.
     * @return array an array of information about the players.
     */
    public function getPlayers($page, $perPage)
    {
        if (! is_null($this->cache)) {
            $key = $this->buildCacheKey(Config::getValue('cache_key_players'), $page, $perPage);
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->fetch($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection();
        
        //the formula is initial_accumulated_points + results + bonuses - new prizes
        try {
            $players = $db->query(
                'SELECT player_id, id_filelist, name_pokerstars, name_filelist, ' .
                'initial_accumulated_points, ' .
                'member_type ' .
                'FROM players ' .
                'ORDER BY player_id ASC'
            );

            $tmpresults = $db->query(
                'SELECT SUM(points) AS points, player_id ' .
                'FROM results ' .
                'GROUP BY player_id ' .
                'ORDER BY player_id ASC'
            );

            $tmpbonuses = $db->query(
                'SELECT SUM(bonus_value) AS bonus_value, player_id ' .
                'FROM bonus_points ' .
                'GROUP BY player_id ' .
                'ORDER BY player_id ASC'
            );
            
            $tmpprizes = $db->query(
                'SELECT SUM(cost) AS cost, player_id ' .
                'FROM prizes ' .
                'GROUP BY player_id ' .
                'ORDER BY player_id ASC'
            );
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf('calling PlayersContent::getPlayers failed: %s', $e->getMessage()),
                FLPokerException::ERROR
            );
        }
        
        $results = $tmpresults->fetchAll();
        $bonuses = $tmpbonuses->fetchAll();
        $prizes = $tmpprizes->fetchAll();
        
        $final_result = array();
        
        foreach ($players as $player) {
            $currentResults = $this->arrayBinarySearch($results, 'points', $player['player_id']);
            $currentBonuses = $this->arrayBinarySearch($bonuses, 'bonus_value', $player['player_id']);
            $currentPrizes = $this->arrayBinarySearch($prizes, 'cost', $player['player_id']);

            $points = $player['initial_accumulated_points'] + $currentResults + $currentBonuses - $currentPrizes;
            
            $final_result[] = array(
                'player_id' => $player['player_id'],
                'id_filelist' => $player['id_filelist'],
                'name_pokerstars' => $player['name_pokerstars'],
                'name_filelist' => $player['name_filelist'],
                'member_type' => $player['member_type'],
                'points' => $points
            );
        }
        
        //the players must be sorted based on the number of points they have
        $this->arraySortByColumn($final_result, 'points');
        
        //only part of the players is returned, based on the pagination parameters given
        $final_result = array_slice($final_result, ($page - 1) * $perPage, $perPage);
        
        if (! is_null($this->cache)) {
            $key = $this->buildCacheKey(Config::getValue('cache_key_players'), $page, $perPage);
            
            $lifetime = Config::getValue('cache_lifetime_players');
            
            $this->cache->save($key, json_encode($final_result), $lifetime);
        }
        
        return $final_result;
    }
    
    public function getPlayersCount()
    {
        $db = Database::getConnection();
        
        try {
            $players = $db->query('SELECT COUNT(*) AS players FROM players');
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf('calling PlayersContent::getPlayersCount failed: %s', $e->getMessage()),
                FLPokerException::ERROR
            );
        }
        
        foreach ($players as $row) {
            $count = $row['players'];
        }
        
        return $count;
    }
    
    //This function will sort the associative array $arr by the column $col in the $dir direction.
    private function arraySortByColumn(&$arr, $col, $dir = SORT_DESC)
    {
        $sort_col = array();
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }
    
    /**
     * This function will do a binary search on the associative array $array.
     * It will return the value of the $type column if the player_id with the
     * value $elem is found in $array. If not, it will return 0.
     * @param array $array
     * @param string $type
     * @param string $elem
     * @return mixed
     */
    private function arrayBinarySearch($array, $type, $elem)
    {
        $top = sizeof($array) - 1;
        $bot = 0;
        while ($top >= $bot) {
            $p = floor(($top + $bot) / 2);
            if ($array[$p]['player_id'] < $elem) {
                $bot = $p + 1;
            } elseif ($array[$p]['player_id'] > $elem) {
                $top = $p - 1;
            } else {
                return $array[$p][$type];
            }
        }
       
        return 0;
    }
    
    private function buildCacheKey($mainKey, $page, $perPage)
    {
        return $mainKey . $page . '_' . $perPage;
    }
}
