<?php

namespace FileListPoker\Content;

use PDO as PDO;
use PDOException as PDOException;

use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;

/**
 * This class contains functions that will return everything there is to know about a specific tournament.
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
            throw new FLPokerException(
                sprintf(
                    'calling TournamentContent::getTournamentDetails with tournament id %s failed: %s',
                    $tid,
                    $e->getMessage()
                ),
                FLPokerException::ERROR
            );
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
            throw new FLPokerException(
                sprintf(
                    'calling TournamentContent::getTournamentResults with tournament id %s failed: %s',
                    $tid,
                    $e->getMessage()
                ),
                FLPokerException::ERROR
            );
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
            throw new FLPokerException(
                sprintf(
                    'calling TournamentContent::getTournamentBonuses with tournament id %s failed: %s',
                    $tid,
                    $e->getMessage()
                ),
                FLPokerException::ERROR
            );
        }
        
        $bonuses = $bonusesSt->fetchAll();
        
        return $bonuses;
    }
    
    public function getNewTournamentDetails($tid)
    {
        $db = Database::getConnection();
        
        try {
            $tournamentSt = $db->prepare(
                'SELECT t.tournament_id, t.participants, COUNT(r.player_id) AS pcount ' .
                'FROM tournaments t ' .
                'JOIN results r ON t.tournament_id=r.tournament_id ' .
                'WHERE t.tournament_id=?'
            );
            
            $tournamentSt->bindParam(1, $tid, PDO::PARAM_INT);
            $tournamentSt->execute();
            $tournament = $tournamentSt->rowCount() == 0 ? false : $tournamentSt->fetch();
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf(
                    'calling TournamentContent::getNewTournamentDetails with tournament id %s failed: %s',
                    $tid,
                    $e->getMessage()
                ),
                FLPokerException::ERROR
            );
        }
        
        if (is_null($tournament['tournament_id']) || is_null($tournament['participants']) || $tournament['pcount'] > 0) {
            return array();
        }
        
        return $tournament;
    }
}
