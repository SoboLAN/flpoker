<?php

namespace FileListPoker\Pages;

use FileListPoker\Main\Database;

class TournamentPage
{
    public function getTournamentDetails($tid)
    {
        $db = Database::getConnection();
        
        try {
            $tournamentSt = $db->prepare(
                'SELECT tournament_id, YEAR(tournament_date) AS year, ' .
                'MONTH(tournament_date) AS month, DAYOFMONTH(tournament_date) AS day, ' .
                'tournament_type, participants, duration_hours, duration_minutes ' .
                'FROM tournaments ' .
                'WHERE tournament_id=?'
            );
            
            $tournamentSt->bindParam(1, $tid, \PDO::PARAM_INT);
            $tournamentSt->execute();
            $tournament = $tournamentSt->rowCount() == 0 ? false : $tournamentSt->fetch(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        if (! $tournament) {
            return array ();
        }
        
        return array(
            'id' => $tournament->tournament_id,
            'day' => $tournament->day,
            'month' => $tournament->month,
            'year' => $tournament->year,
            'type' => $tournament->tournament_type,
            'duration_hours' => $tournament->duration_hours,
            'duration_minutes' => $tournament->duration_minutes,
            'participants' => $tournament->participants
        );
    }
    
    public function getTournamentResults($tid)
    {
        $db = Database::getConnection();
        
        try {
            $resultsSt = $db->prepare(
                'SELECT r.player_id, p.name_pokerstars, r.points, r.position ' .
                'FROM results r ' .
                'LEFT JOIN players p ON r.player_id = p.player_id ' .
                'WHERE r.tournament_id=? ' .
                'ORDER BY r.position ASC'
            );
            
            $resultsSt->bindParam(1, $tid, \PDO::PARAM_INT);
            $resultsSt->execute();
            
            $results = array();
            while ($row = $resultsSt->fetch(\PDO::FETCH_OBJ)) {
                $results[] = $row;
            }
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        $final_result = array();
        foreach ($results as $result) {
            $final_result[] = array(
                'player_id' => $result->player_id,
                'name_pokerstars' => $result->name_pokerstars,
                'points' => $result->points,
                'position' => $result->position
            );
        }
        
        return $final_result;
    }
    
    public function getTournamentBonuses($tid)
    {
        $db = Database::getConnection();
        
        try {
            $bonusesSt = $db->prepare(
                'SELECT b.player_id, b.bonus_value, b.bonus_description, p.name_pokerstars ' .
                'FROM bonus_points b ' .
                'LEFT JOIN players p ON b.player_id = p.player_id ' .
                'WHERE b.tournament_id=? ' .
                'ORDER BY b.bonus_value ASC'
            );
            
            $bonusesSt->bindParam(1, $tid, \PDO::PARAM_INT);
            $bonusesSt->execute();
            
            $bonuses = array();
            while ($row = $bonusesSt->fetch(\PDO::FETCH_OBJ)) {
                $bonuses[] = array(
                    'player_id' => $row->player_id,
                    'name_pokerstars' => $row->name_pokerstars,
                    'bonus_value' => $row->bonus_value,
                    'bonus_description' => $row->bonus_description
                );
            }
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        return $bonuses;
    }
}