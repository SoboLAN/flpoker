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

if (! isset($_POST['flpokerpassword']) || $_POST['flpokerpassword'] !== $adminPass) {
    die('nice try.');
}

if (! isset($_POST['tournamentdate']) ||
    ! isset($_POST['participants']) ||
    ! isset($_POST['hours']) ||
    ! isset($_POST['minutes']) ||
    ! isset($_POST['type']))
{
    die('Some data is missing.');
}

if (! in_array($_POST['type'], array('regular', 'special'))) {
    die('invalid tournament type');
}

try {
    $db = Database::getConnection();
    
    $insertSt = $db->prepare(
        'INSERT INTO tournaments (tournament_id, tournament_date, tournament_type, participants, ' .
        'duration) ' .
        'VALUES ' .
        '(NULL, ?, ?, ?, ?)'
    );
		
	$duration = 60 * $_POST['hours'] + $_POST['minutes'];
    
    $insertSt->bindParam(1, $_POST['tournamentdate'], PDO::PARAM_STR);
    $insertSt->bindParam(2, $_POST['type'], PDO::PARAM_STR);
    $insertSt->bindParam(3, $_POST['participants'], PDO::PARAM_INT);
    $insertSt->bindParam(4, $duration, PDO::PARAM_INT);
    
    $insertSt->execute();

    if ($insertSt->rowCount() !== 1) {
        Logger::log('adding tournament failed with $_POST = ' . print_r($_POST, true) . ': ' . $e->getMessage());
        header('Location: 500.shtml');
		exit();
    }
    
    $getIdStatement = $db->prepare('SELECT MAX(tournament_id) AS tournament_id FROM tournaments');
    $getIdStatement->execute ();
    
    $id = $getIdStatement->fetch(PDO::FETCH_OBJ)->tournament_id;
} catch (PDOException $e) {
    Logger::log('adding tournament failed with $_POST = ' . print_r($_POST, true) . ': ' . $e->getMessage());
    header('Location: 500.shtml');
	exit();
}

echo "Added tournament with ID $id ({$_POST['tournamentdate']}).";