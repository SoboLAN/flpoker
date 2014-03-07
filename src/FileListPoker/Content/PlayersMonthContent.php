<?php

namespace FileListPoker\Content;

use PDOException;

use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;

/**
 * This class contains functions that will return information about the Players of the Month.
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class PlayersMonthContent
{
    /**
     * Returns an associative array of information about all the players of the month.
     * @return array of information about the players of the month. Will contain:
     * <ul>
     * <li>player's ID</li>
     * <li>FileList ID</li>
     * <li>FileList Name</li>
     * <li>PokerStars Name</li>
     * <li>month of obtaining the distinction</li>
     * <li>year of obtaining the distinction</li>
     * </ul>
     */
    public function getPlayersOfTheMonth()
    {
        $db = Database::getConnection();

        try {
            $players = $db->query(
                'SELECT m.player_id, m.award_month, m.award_year, ' .
                'p.id_filelist, p.name_filelist, p.name_pokerstars, p.member_type, ' .
                    '(SELECT SUM(r.points) ' .
                    'FROM results r ' .
                    'JOIN tournaments t ON r.tournament_id=t.tournament_id ' .
                    'WHERE MONTH(t.tournament_date)=m.award_month ' .
                    'AND YEAR(t.tournament_date)=m.award_year ' .
                    'AND r.player_id=m.player_id) AS points ' .
                'FROM players_of_the_month m ' .
                'JOIN players p ON m.player_id = p.player_id ' .
                'ORDER BY m.award_year DESC, m.award_month DESC'
            );
        } catch (PDOException $e) {
            $message = "calling PlayersMonthContent::getPlayersOfTheMonth failed";
            Logger::log("$message: " . $e->getMessage());
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $final_result = array();
        
        foreach ($players as $player) {
            $final_result[] = array(
                'id' => $player->player_id,
                'id_filelist' => $player->id_filelist,
                'name_filelist' => $player->name_filelist,
                'name_pokerstars' => $player->name_pokerstars,
                'member_type' => $player->member_type,
                'month' => $player->award_month,
                'year' => $player->award_year,
                'points' => $player->points
            );
        }
        
        return $final_result;
    }
}
