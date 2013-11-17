<?php

require_once 'autoload.php';
use FileListPoker\Main\Database;

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
        'ORDER BY points DESC ' .
        'LIMIT 5');
} catch (\PDOException $e) {
    die('Something went very wrong.');
}

$rows = array();
foreach ($result as $r) {
    $rows[] = $r;
}

if (count($rows) == 0) {
    exit;
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
    <tr class="with-border"><td class="with-border">Player</td><td class="with-border">Points</td></tr>

<?php
    foreach ($rows as $row) {
        echo '<tr>
            <td class="with-border"><a href="http://flpoker.javafling.org/player.php?id=' . $row->player_id . '">' . $row->name_pokerstars . '</a></td>
            <td class="with-border">' . $row->points . '</td></tr>';
    }

?>

    </table>
    </body>
</html>
