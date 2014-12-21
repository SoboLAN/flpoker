<?php

require_once 'autoload.php';

use PDO as PDO;
use PDOException as PDOException;

use FileListPoker\Main\Database;
use FileListPoker\Main\Logger;
use FileListPoker\Main\Config;

if (! Config::getValue('online')) {
    header('Location: maintenance.shtml');
    exit();
}

$adminPass = Config::getValue('admin_pass');

if (! isset($_POST['flpokerpassword']) or $_POST['flpokerpassword'] !== $adminPass) {
    die ('nice try.');
}

if (! isset ($_POST['registrationdate']) or
    ! isset ($_POST['nameps']) or
    ! isset ($_POST['namefl']) or
    ! isset ($_POST['idfl']))
{
    die ('Some data is missing.');
}

try {
    $db = Database::getConnection();
    
    $getSt = $db->prepare ('SELECT 1 FROM players WHERE name_pokerstars=? OR name_filelist=? OR id_filelist=?');
    
    $getSt->bindParam (1, $_POST['nameps'], PDO::PARAM_STR);
    $getSt->bindParam (2, $_POST['namefl'], PDO::PARAM_STR);
    $getSt->bindParam (3, $_POST['idfl'], PDO::PARAM_INT);
    
    $getSt->execute ();
    
    if ($getSt->rowCount() > 0) {
        die ('This PokerStars name, FileList name or FileList ID already exist in the database');
    }
    
    $insertSt = $db->prepare(
        'INSERT INTO players (player_id, name_pokerstars, name_filelist, id_filelist, member_type, ' .
        'initial_accumulated_points, initial_spent_points, join_date, is_member_of_club) ' .
        'VALUES ' .
        '(NULL, ?, ?, ?, \'regular\', 0, 0, ?, 1)'
    );
    
    $insertSt->bindParam (1, $_POST['nameps'], PDO::PARAM_STR);
    $insertSt->bindParam (2, $_POST['namefl'], PDO::PARAM_STR);
    $insertSt->bindParam (3, $_POST['idfl'], PDO::PARAM_INT);
    $insertSt->bindParam (4, $_POST['registrationdate'], PDO::PARAM_STR);
    
    $insertSt->execute ();

    if ($insertSt->rowCount () !== 1) {
        die('There was an error while adding the player');
    }
    
    $getIdStatement = $db->prepare ('SELECT MAX(player_id) AS player_id FROM players');
    $getIdStatement->execute ();
    
    $id = $getIdStatement->fetch (PDO::FETCH_OBJ)->player_id;
} catch (PDOException $e) {
    Logger::log('adding player failed with $_POST = ' . print_r($_POST, true) . ': ' . $e->getMessage());
    header('Location: 500.shtml');
    exit();
}

echo "Added player with ID $id ({$_POST['nameps']}).";