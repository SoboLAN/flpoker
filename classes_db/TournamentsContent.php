<?php

namespace FileListPoker\Content;

use PDO as PDO;
use PDOException as PDOException;

use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;

/**
 * This class contains functions that return information about all the tournaments.
 */
class TournamentsContent
{
    public function getTournaments($page, $perPage)
    {
        $db = Database::getConnection();

        $limitStart = ($page - 1) * $perPage;
        
        try {
            $tmptournamentsSt = $db->prepare(
                'SELECT tournament_id, YEAR(tournament_date) AS year, ' .
                'MONTH(tournament_date) AS month, DAYOFMONTH(tournament_date) AS day, ' .
                'tournament_type, participants ' .
                'FROM tournaments ' .
                'ORDER BY tournament_date DESC ' .
                'LIMIT ?, ?'
            );
            
            $tmptournamentsSt->bindParam(1, $limitStart, PDO::PARAM_INT);
            $tmptournamentsSt->bindParam(2, $perPage, PDO::PARAM_INT);
            
            $tmptournamentsSt->execute();
            
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf('calling TournamentsContent::getContent failed: %s', $e->getMessage()),
                FLPokerException::ERROR
            );
        }

        $final_result = $tmptournamentsSt->fetchAll();
        
        return $final_result;
    }
    
    public function getTournamentCount()
    {
        $db = Database::getConnection();
        
        try {
            $tournamentsSt = $db->query('SELECT COUNT(*) AS t_count FROM tournaments');

        } catch (PDOException $e) {
            $message = "calling TournamentsContent::getTournamentCount failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $tournaments = $tournamentsSt->fetch();
        
        return $tournaments['t_count'];
    }
    
    public function addTournamentResults($tournamentId, array $results)
    {
        $db = Database::getConnection();
        
        try {
            $insertSt = $db->prepare(
                'INSERT INTO results(player_id, tournament_id, points, position, kos) ' .
                'VALUES (:pid, :tid, :points, :position, :kos)'
            );
            
            foreach ($results as $result) {
                $insertSt->execute(
                    array(
                        'pid' => $result['player'],
                        'tid' => $tournamentId,
                        'points' => $result['points'],
                        'position' => $result['position'],
                        'kos' => $result['kos']
                    )
                );
            }
        } catch (PDOException $e) {
            $message = "calling TournamentsContent::addTournamentResults failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
    }
}
