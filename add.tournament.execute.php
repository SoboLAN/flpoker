<?php

if (! isset($_POST['flpokerpassword']) or $_POST['flpokerpassword'] !== 'myfladminpass8uhb') {
    die ('nice try.');
}

if (! isset ($_POST['tournamentdate']) or
    ! isset ($_POST['participants']) or
    ! isset ($_POST['hours']) or
    ! isset ($_POST['minutes']))
{
    die ('Some data is missing.');
}

require_once 'autoload.php';
use FileListPoker\Main\Database;
use FileListPoker\Main\Logger;

try {
    $db = Database::getConnection();
    
    $insertSt = $db->prepare (
        'INSERT INTO tournaments (tournament_id, tournament_date, tournament_type, participants, ' .
        'duration_hours, duration_minutes) ' .
        'VALUES ' .
        '(NULL, ?, \'regular\', ?, ?, ?)');
    
    $insertSt->bindParam (1, $_POST['tournamentdate'], \PDO::PARAM_STR);
    $insertSt->bindParam (2, $_POST['participants'], \PDO::PARAM_INT);
    $insertSt->bindParam (3, $_POST['hours'], \PDO::PARAM_INT);
    $insertSt->bindParam (4, $_POST['minutes'], \PDO::PARAM_INT);
    
    $insertSt->execute ();

    if ($insertSt->rowCount () !== 1) {
        Logger::log('adding tournament failed with $_POST = ' . print_r($_POST, true) . ': ' . $e->getMessage());
        die('There was an error');
    }
    
    $getIdStatement = $db->prepare ('SELECT MAX(tournament_id) AS tournament_id FROM tournaments');
    $getIdStatement->execute ();
    
    $id = $getIdStatement->fetch (\PDO::FETCH_OBJ)->tournament_id;
} catch (\PDOException $e) {
    Logger::log('adding tournament failed with $_POST = ' . print_r($_POST, true) . ': ' . $e->getMessage());
    die('There was an error');
}

echo "Added tournament with ID $id ({$_POST['tournamentdate']}).";