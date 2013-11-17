<?php

require_once 'autoload.php';
use FileListPoker\Main\Database;

$db = Database::getConnection();

try {
    $result = $db->query('SELECT r.player_id, p.name_pokerstars, ' .
        'COUNT( t.tournament_id ) AS final_tables ' .
        'FROM results r ' .
        'JOIN players p ON r.player_id = p.player_id ' .
        'JOIN tournaments t ON r.tournament_id = t.tournament_id ' .
        'WHERE r.position <= 9 ' .
        'AND MONTH(t.tournament_date) = MONTH(NOW()) ' .
        'AND YEAR(t.tournament_date) = YEAR(NOW()) ' .
        'GROUP BY r.player_id ' .
        'ORDER BY final_tables DESC');
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
    <tr class="with-border"><td class="with-border">Player</td><td class="with-border">Final Tables</td></tr>

<?php
    foreach ($rows as $row) {
        echo '<tr>
            <td class="with-border"><a href="http://flpoker.javafling.org/player.php?id=' . $row->player_id . '">' . $row->name_pokerstars . '</a></td>
            <td class="with-border">' . $row->final_tables . '</td></tr>';
    }

?>

    </table>
    </body>
</html>
