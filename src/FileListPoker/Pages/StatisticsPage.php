<?php

namespace FileListPoker\Pages;

use FileListPoker\Main\Database;
use FileListPoker\Main\Config;
use FileListPoker\Main\CacheDB;

class StatisticsPage
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
                'SELECT ext.tournament_id, ext.participants, MONTH(ext.tournament_date) AS month, ' .
                'YEAR(ext.tournament_date) AS year, DAYOFMONTH( ext.tournament_date ) AS day, ' .
                '(SELECT avg(internal.participants) ' .
                'FROM tournaments internal ' .
                'WHERE internal.tournament_id <= ext.tournament_id) AS average_participants ' .
                'FROM tournaments ext ' .
                'ORDER BY tournament_id ASC'
            );
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        $results = array();
        foreach ($tmpresults as $r) {
            $results[] = array(
                'tournament_id' => $r->tournament_id,
                'participants' => $r->participants,
                'day' => $r->day,
                'month' => $r->month,
                'year' => $r->year,
                'average_participants' => $r->average_participants
            );
        }
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_tournament_graph');
            
            $lifetime = Config::getValue('cache_lifetime_tournament_graph');
            
            $this->cache->save($key, json_encode($results), $lifetime);
        }
        
        return $results;
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
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        $results = array();
        foreach ($tmpresults as $r) {
            $results[] = array(
                'nr_players' => $r->nr_players,
                'join_year' => $r->join_year,
                'join_month' => $r->join_month
            );
        }
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_registrations_graph');
            
            $lifetime = Config::getValue('cache_lifetime_registrations_graph');
            
            $this->cache->save($key, json_encode($results), $lifetime);
        }
        
        return $results;
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
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        $totalPlayers = 0;
        foreach ($tmpplayercount as $pCount) {
            $totalPlayers = $pCount->count;
        }
        
        $totalTournaments = 0;
        foreach ($tmptournamentcount as $tCount) {
            $totalTournaments = $tCount->count;
        }
        
        $totalSpent = 0;
        foreach ($tmpspent1 as $spent) {
            $totalSpent += $spent->players_spent;
        }
        
        foreach ($tmpspent2 as $spent) {
            $totalSpent += $spent->cost;
        }
        
        foreach ($tmpallpoints as $r) {
            $results = array(
                'total_points' => $r->total_points,
                'total_players' => $totalPlayers,
                'total_tournaments' => $totalTournaments,
                'total_spent' => $totalSpent
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
