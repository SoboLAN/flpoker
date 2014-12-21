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

if (! isset($_POST['player']) || ! isset($_POST['thedate'])) {
    die('Some data is missing.');
}

try {
    $db = Database::getConnection();
    
    $getIdStatement = $db->prepare('SELECT player_id FROM players WHERE name_pokerstars=?');
    $getIdStatement->bindParam(1, $_POST['player'], PDO::PARAM_STR);
    $getIdStatement->execute();

    if ($getIdStatement->rowCount() !== 1) {
        die('player does not exist');
    } else {
        $pid = $getIdStatement->fetch(PDO::FETCH_OBJ)->player_id;
    }
    
    $date = \DateTime::createFromFormat("Y-m-d\TH:i:s", $_POST['thedate'] . '-15T00:00:00');
    if ($date == false) {
        die('invalid date');
    } else {
        $month = $date->format('m');
        $year = $date->format('Y');
    }

    $insertSt = $db->prepare(
        'INSERT INTO players_of_the_month (player_of_the_month_id, player_id, award_month, award_year) ' .
        'VALUES ' .
        '(NULL, ?, ?, ?)'
    );

    $insertSt->bindParam(1, $pid, PDO::PARAM_INT);
    $insertSt->bindParam(2, $month, PDO::PARAM_INT);
    $insertSt->bindParam(3, $year, PDO::PARAM_INT);

    $insertSt->execute();
    $rows = $insertSt->rowCount();
} catch (PDOException $e) {
    Logger::log('adding player of the month failed with $_POST = ' . print_r($_POST, true) . ': ' . $e->getMessage());
    header('Location: 500.shtml');
    exit();
}

echo "Added $rows player(s) of the month";