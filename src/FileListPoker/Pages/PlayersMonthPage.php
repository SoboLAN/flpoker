<?php

namespace FileListPoker\Pages;

use FileListPoker\Main\Database;

/**
 * This class contains functions that will return information about the Players of the Month.
 */
class PlayersMonthPage
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
    public function getContent()
    {
        $db = Database::getConnection()->getPDO();

        try {
            $players = $db->query(
                'SELECT m.player_id, m.award_month, m.award_year, ' .
                'p.id_filelist, p.name_filelist, p.name_pokerstars ' .
                'FROM players_of_the_month m ' .
                'JOIN players p ON m.player_id = p.player_id ' .
                'ORDER BY award_year DESC , award_month DESC'
            );
        } catch (\PDOException $e) {
            die('There was a problem while performing database queries');
        }
        
        $final_result = array();
        
        foreach ($players as $player) {
            $final_result[] = array(
                'id' => $player->player_id,
                'id_filelist' => $player->id_filelist,
                'name_filelist' => $player->name_filelist,
                'name_pokerstars' => $player->name_pokerstars,
                'month' => $player->award_month,
                'year' => $player->award_year
            );
        }
        
        return $final_result;
    }
}
