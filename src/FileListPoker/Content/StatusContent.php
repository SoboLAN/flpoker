<?php

namespace FileListPoker\Content;

use PDO;
use PDOException;

use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;

/**
 * This class contains functions that will return the status of the current month
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class StatusContent
{
    public function getCurrentStandings()
    {
        $db = Database::getConnection();
        
        try {
            $result = $db->query('SELECT p.player_id, p.name_pokerstars, SUM(r.points) AS points ' .
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
            
            $standings = array();
            
            while($row = $result->fetch(PDO::FETCH_OBJ)) {
                $standings[] = array(
                    'player_id' => $row->player_id,
                    'name_pokerstars' => $row->name_pokerstars,
                    'points' => $row->points
                );
            }
            
        } catch (PDOException $e) {
            $message = "calling StatusContent::getCurrentStandings failed";
            Logger::log("$message: " . $e->getMessage());
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        if (count($standings) == 0) {
            return array();
        }
        
        $finalResult = array();

        for ($i = 0, $position = 0; $i < count($standings); $i++) {

            $finalResult[$position] = array();

            $finalResult[$position][] = $standings[$i];

            //if there is a next player (we maybe on the last) and that player has the
            //same number of points as the current one, then it means that the 2 players
            //occupy the same position
            while (isset($standings[$i]) and
                    isset($standings[$i + 1]) and
                    $standings[$i]['points'] == $standings[$i + 1]['points']) {
                $finalResult[$position][] = $standings[$i + 1];
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
            
            $finalTables = array();
            
            while($row = $result->fetch(PDO::FETCH_OBJ)) {
                $finalTables[] = array(
                    'player_id' => $row->player_id,
                    'name_pokerstars' => $row->name_pokerstars,
                    'final_tables' => $row->final_tables
                );
            }
            
        } catch (PDOException $e) {
            $message = "calling StatusContent::getFinalTables failed";
            Logger::log("$message: " . $e->getMessage());
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        return $finalTables;
    }
}
