<?php

namespace FileListPoker\Content;

use PDO as PDO;
use PDOException as PDOException;

use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;

/**
 * This class contains functions that return information about all the tournaments.
 * @author Radu Murzea <radu.murzea@gmail.com>
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
            $message = "calling TournamentsContent::getContent failed";
            Logger::log("$message: " . $e->getMessage());
            throw new FLPokerException($message, FLPokerException::ERROR);
        }

        $final_result = array();
        while ($row = $tmptournamentsSt->fetch(PDO::FETCH_ASSOC)) {
            $final_result[] = $row;
        }

        return $final_result;
    }
    
    public function getTournamentCount()
    {
        $db = Database::getConnection();
        
        try {
            $tournaments = $db->query('SELECT COUNT(*) AS t_count FROM tournaments');

        } catch (PDOException $e) {
            $message = "calling TournamentsContent::getTournamentCount failed";
            Logger::log("$message: " . $e->getMessage());
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        foreach ($tournaments as $row) {
            $count = $row['t_count'];
        }
        
        return $count;
    }
}
