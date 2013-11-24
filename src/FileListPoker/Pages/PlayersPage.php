<?php

namespace FileListPoker\Pages;

use FileListPoker\Main\Database;
use FileListPoker\Main\Config;
use FileListPoker\Main\CacheDB;

/**
 * This class contains functions that will return information about all the players.
 */
class PlayersPage
{
    private $cache;
    
    private $results;
    private $bonuses;
    private $prizes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->results = array();
        $this->bonuses = array();
        $this->prizes = array();
        
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
     * Returns an associative array of information about all the players.
     * @return array an array of information about the players. Will contain:
     * <ul>
     * <li>player's ID</li>
     * <li>FileList ID</li>
     * <li>FileList Name</li>
     * <li>PokerStars Name</li>
     * <li>account Type (regular or admin)</li>
     * <li>day of join date</li>
     * <li>month of join date</li>
     * <li>year of join date</li>
     * <li>current points</li>
     * </ul>
     */
    public function getContent()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->getContent($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection();
        
        //the formula is initial_accumulated_points + results + bonuses - new prizes
        try {
            $players = $db->query(
                'SELECT player_id, id_filelist, name_pokerstars, name_filelist, ' .
                'initial_accumulated_points, MONTH(join_date) AS month, ' .
                'DAYOFMONTH(join_date) AS day, YEAR(join_date) AS year, ' .
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
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        $this->fillArrays($tmpresults, $tmpbonuses, $tmpprizes);
        
        $final_result = array();
        
        foreach ($players as $player) {
            $currentResults = $this->arrayBinarySearch($this->results, 'points', $player->player_id);
            $currentBonuses = $this->arrayBinarySearch($this->bonuses, 'bonus_value', $player->player_id);
            $currentPrizes = $this->arrayBinarySearch($this->prizes, 'cost', $player->player_id);

            $playerPoints = $player->initial_accumulated_points + $currentResults + $currentBonuses - $currentPrizes;
            
            $final_result[] = array(
                'player_id' => $player->player_id,
                'id_filelist' => $player->id_filelist,
                'name_pokerstars' => $player->name_pokerstars,
                'name_filelist' => $player->name_filelist,
                'member_type' => $player->member_type,
                'year' => $player->year,
                'month' => $player->month,
                'day' => $player->day,
                'points' => $playerPoints
            );
        }
        
        $this->arraySortByColumn($final_result, 'points');
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_players');
            
            $lifetime = Config::getValue('cache_lifetime_players');
            
            $this->cache->save($key, json_encode($final_result), $lifetime);
        }
        
        return $final_result;
    }
    
    //this is literally the stupidest thing about PDO.
    //apparently, the result set is actually a Traversable.
    //you must copy it into an array of your own before you can have all
    //sorts of manipulations on it... foreach() is too rigid for that.
    //FUCKING IDIOTS !!!!!!!
    private function fillArrays($tmpresults, $tmpbonuses, $tmpprizes)
    {
        foreach ($tmpresults as $tmpresult) {
            $this->results[] = $tmpresult;
        }
        foreach ($tmpbonuses as $tmpbonus) {
            $this->bonuses[] = $tmpbonus;
        }
        foreach ($tmpprizes as $tmpprize) {
            $this->prizes[] = $tmpprize;
        }
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