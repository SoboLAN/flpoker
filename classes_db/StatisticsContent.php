<?php

namespace FileListPoker\Content;

use PDOException as PDOException;

use FileListPoker\Main\Database;
use FileListPoker\Main\Config;
use FileListPoker\Main\CacheDB;
use FileListPoker\Main\FLPokerException;

/**
 * This class contains function that will return various statistics about the club.
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class StatisticsContent
{
    private $cache;
    
    public function __construct()
    {
        if (Config::getValue('enable_cache')) {
            $cacheType = Config::getValue('cache_type');
        
            if ($cacheType == 'db') {
                $this->cache = new CacheDB();
            }
        }
    }
    
    public function getTournamentsGraph()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_tournament_graph');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->getContent($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection();
        
        try {
            $tmpresults = $db->query(
                'SELECT ROUND(AVG(participants), 2) AS average_participants, ' .
                'MONTH(tournament_date) AS month, YEAR(tournament_date) AS year, ' .
                'CONCAT(MONTH(tournament_date), \'-\', YEAR(tournament_date)) AS tournament_interval ' .
                'FROM tournaments ' .
                'WHERE tournament_type=\'regular\' ' .
                'GROUP BY tournament_interval ' .
                'ORDER BY year ASC, month ASC'
            );
        } catch (PDOException $e) {
            $message = "calling StatisticsContent::getTournamentsGraph failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $result = $tmpresults->fetchAll();
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_tournament_graph');
            
            $lifetime = Config::getValue('cache_lifetime_tournament_graph');
            
            $this->cache->save($key, json_encode($result), $lifetime);
        }
        
        return $result;
    }
    
    public function getRegistrationsGraph()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_registrations_graph');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->getContent($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection();
        
        try {
            $tmpresults = $db->query(
                'SELECT COUNT(player_id) AS nr_players, YEAR(join_date) AS join_year, ' .
                'MONTH(join_date) AS join_month, ' .
                'CONCAT(YEAR(join_date), \'-\', MONTH(join_date)) AS glued ' .
                'FROM players ' .
                'WHERE join_date IS NOT NULL ' .
                'GROUP BY glued ' .
                'ORDER BY join_year ASC, join_month ASC'
            );
        } catch (PDOException $e) {
            $message = "calling StatisticsContent::getRegistrationsGraph failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $result = $tmpresults->fetchAll();
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_registrations_graph');
            
            $lifetime = Config::getValue('cache_lifetime_registrations_graph');
            
            $this->cache->save($key, json_encode($result), $lifetime);
        }
        
        return $result;
    }
    
    public function getAggressionGraph()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_aggresion_graph');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->getContent($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection();
        
        try {
            //aggression factor is the average number of players eliminated per hour in a
            //tournament
            //query will calculate the average of the aggression factor for a given month
            $tmpresults = $db->query(
                'SELECT ROUND(AVG(60 * participants / duration), 2) AS aggression_factor, ' .
                'MONTH(tournament_date) AS tournament_month, ' .
                'YEAR(tournament_date) AS tournament_year, ' .
                'CONCAT(MONTH(tournament_date), \'-\', YEAR(tournament_date)) AS tournament_period ' .
                'FROM tournaments ' .
                'WHERE duration IS NOT NULL AND tournament_type=\'regular\' ' .
                'GROUP BY tournament_period ' .
                'ORDER BY tournament_year ASC, tournament_month ASC'
            );
        } catch (PDOException $e) {
            $message = "calling StatisticsContent::getAggressionGraph failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $result = $tmpresults->fetchAll();
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_aggresion_graph');
            
            $lifetime = Config::getValue('cache_lifetime_aggresion_graph');
            
            $this->cache->save($key, json_encode($result), $lifetime);
        }
        
        return $result;
    }
    
    public function getGeneralStatistics()
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_general_stats');
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->getContent($key), true);
                
                return $content;
            }
        }
        
        $db = Database::getConnection();
        
        try {
            $tmpallpoints = $db->query(
                'SELECT p.initial_accumulated_points + p.initial_spent_points + r.points + b.bonus_value ' .
                'AS total_points ' .
                'FROM ' .
                '(SELECT SUM(initial_accumulated_points) AS initial_accumulated_points, ' .
                'SUM(initial_spent_points) AS initial_spent_points ' .
                'FROM players) p, ' .
                '(SELECT SUM(points) AS points FROM results) r, ' .
                '(SELECT SUM(bonus_value) AS bonus_value FROM bonus_points) b'
            );
            
            $tmpplayercount = $db->query('SELECT COUNT(*) AS count FROM players');
            
            $tmptournamentcount = $db->query('SELECT COUNT(*) AS count FROM tournaments');
            
            $tmpspent1 = $db->query('SELECT SUM(initial_spent_points) AS players_spent FROM players');
            
            $tmpspent2 = $db->query('SELECT SUM(cost) AS cost FROM prizes');
        } catch (PDOException $e) {
            $message = "calling StatisticsContent::getGeneralStatistics failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $totalPlayers = $tmpplayercount->fetch();
        $totalTournaments = $tmptournamentcount->fetch();
        $spent1 = $tmpspent1->fetch();
        $spent2 = $tmpspent2->fetch();
        
        foreach ($tmpallpoints as $r) {
            $results = array(
                'total_points' => $r['total_points'],
                'total_players' => $totalPlayers['count'],
                'total_tournaments' => $totalTournaments['count'],
                'total_spent' => $spent1['players_spent'] + $spent2['cost']
            );
        }
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_general_stats');
            
            $lifetime = Config::getValue('cache_lifetime_general_stats');
            
            $this->cache->save($key, json_encode($results), $lifetime);
        }
        
        return $results;
    }
}