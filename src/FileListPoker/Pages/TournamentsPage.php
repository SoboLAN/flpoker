<?php

namespace FileListPoker\Pages;

use FileListPoker\Main\Database;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;

/**
 * This class contains functions that return information about all the tournaments.
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class TournamentsPage
{
    public function getContent()
    {
        $db = Database::getConnection();

        try {
            $tmptournaments = $db->query(
                'SELECT tournament_id, YEAR(tournament_date) AS year, ' .
                'MONTH(tournament_date) AS month, DAYOFMONTH(tournament_date) AS day, ' .
                'tournament_type, participants ' .
                'FROM tournaments ' .
                'ORDER BY tournament_date DESC'
            );
        } catch (\PDOException $e) {
            $message = "calling TournamentsPage::getContent failed";
            Logger::log("$message: " . $e->getMessage());
            throw new FLPokerException($message, FLPokerException::ERROR);
        }

        $final_result = array();
        foreach ($tmptournaments as $tournament) {
            $final_result[] = array(
                'id' => $tournament->tournament_id,
                'day' => $tournament->day,
                'month' => $tournament->month,
                'year' => $tournament->year,
                'type' => $tournament->tournament_type,
                'participants' => $tournament->participants
            );
        }

        return $final_result;
    }
}
