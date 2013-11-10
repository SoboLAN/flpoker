<?php

namespace FileListPoker\Pages;

use FileListPoker\Main\Database;
use FileListPoker\Main\Config;
use FileListPoker\Main\CacheDB;

/**
 * This class contains functions that will return various rankings of players.
 */
class RankingsPage
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
     * Returns an associative array of information about all the players. It will contain
     * the total points that every player has earned ever.
     * @return array the total points that every player has earned ever. Will contain:
     * <ul>
     * <li>player's ID</li>
     * <li>FileList ID</li>
     * <li>FileList Name</li>
     * <li>PokerStars Name</li>
     * <li>points all time for each player</li>
     * </ul>
     */
    public function getTopPlayersAllTime()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players_alltime');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->getContent($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection()->getPDO();
        
        //the formula is initial_accumulated_points + initial_prizes + results + bonuses
        try {
            $players = $db->query(
                'SELECT player_id, id_filelist, name_pokerstars, name_filelist,
                initial_accumulated_points, initial_spent_points ' .
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
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        $results = array();
        $bonuses = array();
        foreach ($tmpresults as $tmpresult) {
            $results[] = $tmpresult;
        }
        foreach ($tmpbonuses as $tmpbonus) {
            $bonuses[] = $tmpbonus;
        }

        $final_result = array();
        
        foreach ($players as $player) {
            $currentResults = $this->arrayBinarySearch($results, 'points', $player->player_id);
            $currentBonuses = $this->arrayBinarySearch($bonuses, 'bonus_value', $player->player_id);

            $playerPoints = $player->initial_accumulated_points + $player->initial_spent_points +
                            $currentResults + $currentBonuses;
            
            $final_result[] = array(
                'player_id' => $player->player_id,
                'id_filelist' => $player->id_filelist,
                'name_pokerstars' => $player->name_pokerstars,
                'name_filelist' => $player->name_filelist,
                'points' => $playerPoints
            );
        }
        
        $this->arraySortByColumn($final_result, 'points');
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players_alltime');
            
            $lifetime = Config::getValue('cache_lifetime_players_alltime');
            
            $this->cache->save($key, json_encode($final_result), $lifetime);
        }
        
        return $final_result;
    }
    
    /**
     * Returns an associative array of information about the 50 most active players.
     * It will contain the total number of tournaments each of those players have "cashed" in.
     * @return array the total number of tournaments each of the top 50 most active players
     * "cashed" in. Will contain:
     * <ul>
     * <li>player's ID</li>
     * <li>PokerStars Name</li>
     * <li>number of tournaments that player cashed in</li>
     * </ul>
     */
    public function getMostActive50Players()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players_mostactive');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->getContent($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection()->getPDO();
        
        try {
            $tmpactive = $db->query(
                'SELECT r.count, p.name_pokerstars, p.player_id FROM ' .
                '(SELECT COUNT(*) AS count, player_id FROM results ' .
                'WHERE player_id IS NOT NULL ' .
                'GROUP BY player_id ' .
                'ORDER BY count DESC) r ' .
                'JOIN players p ON p.player_id=r.player_id ' .
                'LIMIT 50'
            );
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        $results = array();
        foreach ($tmpactive as $r) {
            $results[] = array(
                'player_id' => $r->player_id,
                'name_pokerstars' => $r->name_pokerstars,
                'count' => $r->count
            );
        }
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players_mostactive');
            
            $lifetime = Config::getValue('cache_lifetime_players_mostactive');
            
            $this->cache->save($key, json_encode($results), $lifetime);
        }
        
        return $results;
    }
    
    /**
     * Returns an associative array of information about the 40 players that received the
     * most points in the last 6 months. It will contain the total number of points each
     * of those players received in the last 6 months.
     * @return array will contain:
     * <ul>
     * <li>player's ID</li>
     * <li>PokerStars Name</li>
     * <li>total points that player received in the last 6 months</li>
     * </ul>
     */
    public function getTop40Players6Months()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players_6months');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->getContent($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection()->getPDO();
        
        try {
            $tmp6months = $db->query(
                'SELECT r.player_id, SUM(r.points) AS totalp, p.name_pokerstars ' .
                'FROM results r ' .
                'JOIN tournaments t ON r.tournament_id = t.tournament_id ' .
                'JOIN players p ON r.player_id = p.player_id ' .
                'WHERE DATEDIFF(CURDATE(), tournament_date) <= 182 ' .  //182 days = 6 months
                'GROUP BY r.player_id ' .
                'ORDER BY totalp DESC ' .
                'LIMIT 40'
            );
            
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        $results = array();
        foreach ($tmp6months as $r) {
            $results[] = array(
                'player_id' => $r->player_id,
                'name_pokerstars' => $r->name_pokerstars,
                'totalp' => $r->totalp
            );
        }
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players_6months');
            
            $lifetime = Config::getValue('cache_lifetime_players_6months');
            
            $this->cache->save($key, json_encode($results), $lifetime);
        }
        
        return $results;
    }
    
    /**
     * Returns an associative array of information about the 50 players that reached the
     * most final tables during their membership. It will contain the total number of final
     * tables each of those players reached at.
     * @return array will contain:
     * <ul>
     * <li>player's ID</li>
     * <li>PokerStars Name</li>
     * <li>total number of final tables that player reached at</li>
     * </ul>
     */
    public function getTop50FinalTables()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_final_tables');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->getContent($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection()->getPDO();
        
        try {
            $tmpfinaltables = $db->query(
                'SELECT count(r.tournament_id) AS final_tables, r.player_id, p.name_pokerstars ' .
                'FROM results r ' .
                'JOIN players p ON p.player_id = r.player_id ' .
                'WHERE position <= 9 ' .
                'GROUP BY player_id ' .
                'ORDER BY final_tables DESC ' .
                'LIMIT 50'
            );
            
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        $results = array();
        foreach ($tmpfinaltables as $r) {
            $results[] = array(
                'player_id' => $r->player_id,
                'name_pokerstars' => $r->name_pokerstars,
                'final_tables' => $r->final_tables
            );
        }
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_final_tables');
            
            $lifetime = Config::getValue('cache_lifetime_final_tables');
            
            $this->cache->save($key, json_encode($results), $lifetime);
        }
        
        return $results;
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
    
    //this function will do a binary search on the associative array $array.
    //It will return the value of the $type column if the player_id with the
    //value $elem is found in $array. If not, it will return 0.
    private function arrayBinarySearch($array, $type, $elem)
    {
        $top = sizeof($array) - 1;
        $bot = 0;
        while ($top >= $bot) {
            $p = floor(($top + $bot) / 2);
            if ($array[$p]->player_id < $elem) {
                $bot = $p + 1;
            } elseif ($array[$p]->player_id > $elem) {
                $top = $p - 1;
            } else {
                return $array[$p]->$type;
            }
        }
       
        return 0;
    }
}
