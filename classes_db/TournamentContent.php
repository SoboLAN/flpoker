<?php

namespace FileListPoker\Content;

use PDO as PDO;
use PDOException as PDOException;

use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;

/**
 * This class contains functions that will return everything there is to know about a specific tournament.
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class TournamentContent
{
    public function getTournamentDetails($tid)
    {
        $db = Database::getConnection();
        
        try {
            $tournamentSt = $db->prepare(
                'SELECT tournament_id, YEAR(tournament_date) AS year, ' .
                'MONTH(tournament_date) AS month, DAYOFMONTH(tournament_date) AS day, ' .
                'tournament_type, participants, duration ' .
                'FROM tournaments ' .
                'WHERE tournament_id=?'
            );
            
            $tournamentSt->bindParam(1, $tid, PDO::PARAM_INT);
            $tournamentSt->execute();
            $tournament = $tournamentSt->rowCount() == 0 ? false : $tournamentSt->fetch();
        } catch (PDOException $e) {
            $message = "calling TournamentContent::getTournamentDetails with tournament id $tid failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        if (! $tournament) {
            return array();
        }
        
        return $tournament;
    }
    
    public function getTournamentResults($tid)
    {
        $db = Database::getConnection();
        
        try {
            $resultsSt = $db->prepare(
                'SELECT r.player_id, p.name_pokerstars, r.points, r.position, r.kos ' .
                'FROM results r ' .
                'LEFT JOIN players p ON r.player_id = p.player_id ' .
                'WHERE r.tournament_id=? ' .
                'ORDER BY r.position ASC'
            );
            
            $resultsSt->bindParam(1, $tid, PDO::PARAM_INT);
            $resultsSt->execute();
            
        } catch (PDOException $e) {
            $message = "calling TournamentContent::getTournamentResults with tournament id $tid failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $results = $resultsSt->fetchAll();
        
        return $results;
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
            
            $bonusesSt->bindParam(1, $tid, PDO::PARAM_INT);
            $bonusesSt->execute();
            
        } catch (PDOException $e) {
            $message = "calling TournamentContent::getTournamentBonuses with tournament id $tid failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $bonuses = $bonusesSt->fetchAll();
        
        return $bonuses;
    }
}
