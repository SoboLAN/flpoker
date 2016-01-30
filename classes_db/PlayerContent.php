<?php

namespace FileListPoker\Content;

use FileListPoker\Main\Database;
use FileListPoker\Main\Config;
use FileListPoker\Main\Cache\CacheFactory;
use FileListPoker\Main\FLPokerException;

use Doctrine\Common\Cache\CacheProvider;

use PDO as PDO;
use PDOException as PDOException;

/**
 * This class contains functions that will return information about a particular player.
 */
class PlayerContent
{
    /**
     * @var CacheProvider
     */
    private $cache;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->cache = CacheFactory::getCacheInstance();
    }

    /**
     * Returns an associative array of information about the player identified by the passed parameter.
     * @param int $pid the ID of the wanted player.
     * @return array of information about the player.
     */
    public function getGeneral($pid)
    {
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_player_general') . $pid;
            
            if ($this->cache->contains($key)) {
                $content = json_decode($this->cache->fetch($key), true);
                
                if (is_null($content)) {
                    $message = "cached value for key $key is invalid";
                    throw new FLPokerException($message, FLPokerException::ERROR);
                }
                
                return $content;
            }
        }
        
        $db = Database::getConnection();

        try {
            $playerInfoSt = $db->prepare(
                'SELECT id_filelist, name_pokerstars, name_filelist, ' .
                'initial_accumulated_points, initial_spent_points, ' .
                'MONTH(join_date) AS month, ' .
                'DAYOFMONTH(join_date) AS day, YEAR(join_date) AS year, ' .
                'member_type ' .
                'FROM players ' .
                'WHERE player_id=? ' .
                'ORDER BY player_id ASC'
            );

            $resultsSt = $db->prepare('SELECT SUM(points) AS points FROM results WHERE player_id=?');

            $bonusesSt = $db->prepare(
                'SELECT SUM(bonus_value) AS bonus_value ' .
                'FROM bonus_points ' .
                'WHERE player_id=?'
            );

            $prizesSt = $db->prepare(
                'SELECT SUM(cost) AS cost ' .
                'FROM prizes ' .
                'WHERE player_id=?'
            );

            $tournamentCountSt = $db->prepare(
                'SELECT tournaments.tcount, final_tables.fcount, knockouts.koscount ' .
                'FROM ' .
                    '(SELECT COUNT(*) AS tcount ' .
                     'FROM results ' .
                     'WHERE player_id=?) tournaments, ' .
                    '(SELECT COUNT(*) AS fcount ' .
                     'FROM results ' .
                     'WHERE player_id=? AND position <= 9) final_tables, ' .
                    '(SELECT SUM(kos) AS koscount ' .
                     'FROM results ' .
                     'WHERE player_id=?) knockouts'
            );
            
            $playerMonthSt = $db->prepare('SELECT COUNT(*) AS pomcount FROM players_of_the_month WHERE player_id=?');

            $playerInfoSt->bindParam(1, $pid, PDO::PARAM_INT);
            $playerInfoSt->execute();
            $playerInfo = $playerInfoSt->rowCount() == 0 ? false : $playerInfoSt->fetch();
            
            $resultsSt->bindParam(1, $pid, PDO::PARAM_INT);
            $resultsSt->execute();
            $results = $resultsSt->fetch();
            
            $bonusesSt->bindParam(1, $pid, PDO::PARAM_INT);
            $bonusesSt->execute();
            $bonuses = $bonusesSt->fetch();
            
            $prizesSt->bindParam(1, $pid, PDO::PARAM_INT);
            $prizesSt->execute();
            $prizes = $prizesSt->fetch();
            
            $tournamentCountSt->bindParam(1, $pid, PDO::PARAM_INT);
            $tournamentCountSt->bindParam(2, $pid, PDO::PARAM_INT);
            $tournamentCountSt->bindParam(3, $pid, PDO::PARAM_INT);
            $tournamentCountSt->execute();
            $tCountRow = $tournamentCountSt->fetch();
            $tournamentCount = $tCountRow['tcount'];
            $finalTables = $tCountRow['fcount'];
            $knockouts = is_null($tCountRow['koscount']) ? 0 : $tCountRow['koscount'];
            
            $playerMonthSt->bindParam(1, $pid, PDO::PARAM_INT);
            $playerMonthSt->execute();
            $pomCount = $playerMonthSt->fetch();
            
        } catch (PDOException $e) {
            $message = "calling PlayerContent::getGeneral with player id $pid failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }

        if (! $playerInfo) {
            return array();
        }

        $points = $playerInfo['initial_accumulated_points']
            + $results['points']
            + $bonuses['bonus_value']
            - $prizes['cost'];
        $pointsAllTime = $playerInfo['initial_accumulated_points']
            + $playerInfo['initial_spent_points']
            + $results['points']
            + $bonuses['bonus_value'];
        
        $finalResult = array_merge(
            $playerInfo,
            array(
                'points' => $points,
                'tournament_count' => $tournamentCount,
                'final_tables' => $finalTables,
                'knockouts' => $knockouts,
                'points_all_time' => $pointsAllTime,
                'pomcount' => $pomCount['pomcount']
            )
        );
        
        if (! is_null($this->cache)) {
            $key = Config::getValue('cache_key_player_general') . $pid;
            
            $lifetime = Config::getValue('cache_lifetime_player_general');
            
            $this->cache->save($key, json_encode($finalResult), $lifetime);
        }
        
        return $finalResult;
    }

    /**
     * Returns an array of associative arrays containing the tournament history of the player
     * identified with the passed parameter.
     * @param int $pid ID of the player.
     * @return array an array of associative arrays containing details about the player's
     * tournament history.
     * If the player never got points in any tournaments, an empty array will be returned.
     */
    public function getTournamentHistory($pid)
    {
        $db = Database::getConnection();
        
        try {
            $historySt = $db->prepare(
                'SELECT t.tournament_id, DAYOFMONTH(t.tournament_date) AS day, ' .
                'MONTH(t.tournament_date) AS month, YEAR(t.tournament_date) AS year, ' .
                'r.points, r.position, r.kos ' .
                'FROM tournaments t ' .
                'JOIN results r ON t.tournament_id=r.tournament_id ' .
                'WHERE r.player_id=? ' .
                'ORDER BY t.tournament_date DESC'
            );
            
            $historySt->bindParam(1, $pid, PDO::PARAM_INT);
            $historySt->execute();
            
        } catch (PDOException $e) {
            $message = "calling PlayerContent::getTournamentHistory with player id $pid failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $history = $historySt->fetchAll();
        
        return $history;
    }

    /**
     * Returns an array of associative arrays containing the bonuses of the player
     * identified with the passed parameter.
     * @param int $pid ID of the player.
     * @return array an array of associative arrays containing details about the player's
     * bonuses.
     * If the player never got bonuses in any tournaments, an empty array will be returned.
     */
    public function getBonuses($pid)
    {
        $db = Database::getConnection();
        
        try {
            $bonusesSt = $db->prepare(
                'SELECT bonus_value, tournament_id, bonus_description, ' .
                'DAYOFMONTH(bonus_date) AS day, MONTH(bonus_date) AS month , ' .
                'YEAR(bonus_date) AS year, ' .
                'UNIX_TIMESTAMP(bonus_date) AS stamp ' .
                'FROM bonus_points ' .
                'WHERE player_id=? ' .
                'ORDER BY stamp ASC'
            );
            
            $bonusesSt->bindParam(1, $pid, PDO::PARAM_INT);
            $bonusesSt->execute();
            
        } catch (PDOException $e) {
            $message = "calling PlayerContent::getBonuses with player id $pid failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $bonuses = $bonusesSt->fetchAll();
        
        return $bonuses;
    }

    /**
     * Returns an array of associative arrays containing the prizes of the player
     * identified with the passed parameter.
     * @param int $pid ID of the player.
     * @return array an array of associative arrays containing details about the player's
     * prizes.
     * If the player never bought anything, an empty array will be returned.
     */
    public function getPrizes($pid)
    {
        $db = Database::getConnection();

        try {
            $prizesSt = $db->prepare(
                'SELECT prize, cost, ' .
                'DAYOFMONTH(date_bought) AS day, ' .
                'MONTH(date_bought) AS month , ' .
                'YEAR(date_bought) AS year ' .
                'FROM prizes ' .
                'WHERE player_id=?'
            );
            
            $prizesSt->bindParam(1, $pid, PDO::PARAM_INT);
            $prizesSt->execute();
            
        } catch (PDOException $e) {
            $message = "calling PlayerContent::getPrizes with player id $pid failed: " . $e->getMessage();
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $prizes = $prizesSt->fetchAll();
        
        return $prizes;
    }
}
