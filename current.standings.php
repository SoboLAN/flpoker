<?php

require_once 'autoload.php';
use FileListPoker\Main\Database;
use FileListPoker\Main\Logger;

$db = Database::getConnection();

try {
    $result = $db->query('SELECT p.player_id, p.name_pokerstars, SUM(r.points) AS points ' .
        'FROM players p ' .
        'JOIN results r ON p.player_id = r.player_id ' .
        'WHERE r.tournament_id IN ' .
        '(SELECT t.tournament_id ' .
        'FROM tournaments t ' .
        'WHERE MONTH(t.tournament_date) = MONTH(NOW()) ' .
        'AND YEAR(t.tournament_date) = YEAR(NOW())) ' .
        'GROUP BY p.player_id ' .
        'ORDER BY points DESC'
    );
} catch (\PDOException $e) {
    Logger::log('accessing current.standings.php failed');
    header('Location: 500.shtml');
}

$rows = array();
foreach ($result as $r) {
    $rows[] = $r;
}

if (count($rows) == 0) {
    exit;
}

$finalResult = array();

for ($i = 0, $position = 0; $i < count($rows); $i++) {
    
    $finalResult[$position] = array();
    
    $finalResult[$position][] = $rows[$i];
    
    //if there is a next player (we maybe on the last) and that player has the
    //same number of points as the current one, then it means that the 2 players
    //occupy the same position
    while (isset($rows[$i]) and
            isset($rows[$i + 1]) and
            $rows[$i]->points == $rows[$i + 1]->points) {
        $finalResult[$position][] = $rows[$i + 1];
        $i++;
    }
    
    $position++;
    if ($position >= 5) {
        break;
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <style type="text/css">
            .with-border {
                border: 1px solid #999;
            }
            #main-table {
                text-align: center;
                width: 40%;
            }
        </style>
    </head>
    <body>
<table id="main-table">
    <tr class="with-border">
        <td class="with-border">Position</td>
        <td class="with-border">Player</td>
        <td class="with-border">Points</td>
    </tr>

<?php
    for ($i = 0; $i < count($finalResult); $i++) {
        
        if (count($finalResult[$i]) > 1) {
            
            $currentPlayers = array();
            foreach ($finalResult[$i] as $player) {
                $currentPlayers[] = '<a href="http://flpoker.javafling.org/player.php?id=' . $player->player_id . '">' . $player->name_pokerstars . '</a>';
            }
            
            echo '<tr>
                <td class="with-border">' . ($i + 1) . '</td>
                <td class="with-border">' . implode(', ', $currentPlayers) . '</td>
                <td class="with-border">' . $finalResult[$i][0]->points . '</td>
            </tr>';
        } else {
            echo '<tr>
            <td class="with-border">' . ($i + 1) . '</td>
            <td class="with-border"><a href="http://flpoker.javafling.org/player.php?id=' . $finalResult[$i][0]->player_id . '">' . $finalResult[$i][0]->name_pokerstars . '</a></td>
            <td class="with-border">' . $finalResult[$i][0]->points . '</td></tr>';
        }
    }

?>

    </table>
    </body>
</html>
