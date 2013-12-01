<?php

if (! isset($_POST['flpokerpassword']) or $_POST['flpokerpassword'] !== 'myfladminpass8uhb') {
    die ('nice try.');
}

require_once 'autoload.php';
use FileListPoker\Main\Database;
use FileListPoker\Main\Logger;

try {
    $db = Database::getConnection();
    
    $i = 1;
    $getIdStatement = $db->prepare ('SELECT player_id FROM players WHERE name_pokerstars=?');
    $insertResults = array();
    $tid = $_POST['tournamentid'];
    while (true) {
        if (! isset($_POST["player$i"]) or empty($_POST["player$i"])) {
            break;
        }
        
        if ($_POST["player$i"] == 'NULL') {
            $id = 'NULL';
        } else {
            $getIdStatement->bindParam (1, $_POST["player$i"], \PDO::PARAM_STR);
            $getIdStatement->execute ();

            if ($getIdStatement->rowCount () !== 1) {
                break;
            } else {
                $id = $getIdStatement->fetch (\PDO::FETCH_OBJ)->player_id;
            }
            
            $getIdStatement->closeCursor();
        }
        
        $points = $_POST['points' . $i];
        $position = $_POST['position' . $i];

        $insertResults[] = "($id, $tid, $points, $position)";

        $i++;
    }

    $result = $db->exec ('INSERT INTO results(player_id, tournament_id, points, position) ' .
                        'VALUES ' .
                        implode (',', $insertResults));
} catch (\PDOException $e) {
    Logger::log('adding result failed with $_POST = ' . print_r($_POST, true) . ': ' . $e->getMessage());
    header('Location: 500.shtml');
	exit();
}

echo "Inserted $result rows.";
