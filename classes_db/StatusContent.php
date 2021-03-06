<?php

namespace FileListPoker\Content;

use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;

use PDOException as PDOException;

/**
 * This class contains functions that will return the status of the current month
 */
class StatusContent
{
    public function getCurrentStandings()
    {
        $db = Database::getConnection();
        
        try {
            $results = $db->query('SELECT p.player_id, p.name_pokerstars, SUM(r.points) AS points ' .
                'FROM players p ' .
                'JOIN results r ON p.player_id = r.player_id ' .
                'WHERE r.tournament_id IN ' .
                '(SELECT t.tournament_id ' .
                'FROM tournaments t ' .
                'WHERE MONTH(t.tournament_date) = MONTH(NOW()) ' .
                'AND YEAR(t.tournament_date) = YEAR(NOW()) ' .
                'AND t.tournament_type=\'regular\') ' .
                'GROUP BY p.player_id ' .
                'ORDER BY points DESC'
            );
            
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf('calling StatusContent::getCurrentStandings failed: %s', $e->getMessage()),
                FLPokerException::ERROR
            );
        }
        
        if (count($results) == 0) {
            return array();
        }
        
        $resultsArray = $results->fetchAll();
        
        $finalResult = array();
        for ($i = 0, $position = 0; $i < count($resultsArray); $i++) {

            $finalResult[$position] = array();

            $finalResult[$position][] = $resultsArray[$i];

            //if there is a next player (we maybe on the last) and that player has the
            //same number of points as the current one, then it means that the 2 players
            //occupy the same position
            while (isset($resultsArray[$i])
                && isset($resultsArray[$i + 1])
                && $resultsArray[$i]['points'] == $resultsArray[$i + 1]['points']
            ) {
                $finalResult[$position][] = $resultsArray[$i + 1];
                $i++;
            }

            $position++;
            if ($position >= 5) {
                break;
            }
        }
        
        return $finalResult;
    }
    
    public function getFinalTables()
    {
        $db = Database::getConnection();
        
        try {
            $result = $db->query('SELECT r.player_id, p.name_pokerstars, ' .
                'COUNT(t.tournament_id) AS final_tables ' .
                'FROM results r ' .
                'JOIN players p ON r.player_id = p.player_id ' .
                'JOIN tournaments t ON r.tournament_id = t.tournament_id ' .
                'WHERE r.position <= 9 ' .
                'AND MONTH(t.tournament_date) = MONTH(NOW()) ' .
                'AND YEAR(t.tournament_date) = YEAR(NOW()) ' .
                'AND t.tournament_type=\'regular\' ' .
                'GROUP BY r.player_id ' .
                'HAVING final_tables > 1 ' .
                'ORDER BY final_tables DESC'
            );
            
        } catch (PDOException $e) {
            throw new FLPokerException(
                sprintf('calling StatusContent::getFinalTables failed: %s', $e->getMessage()),
                FLPokerException::ERROR
            );
        }
        
        return $result->fetchAll();
    }
}
