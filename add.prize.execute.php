<?php

require_once 'autoload.php';
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

if (! isset ($_POST['player']) or
    ! isset ($_POST['prize']) or
    ! isset ($_POST['cost']) or
    ! isset ($_POST['purchasedate']))
{
    die ('Some data is missing.');
}

try {
    $db = Database::getConnection();
    
    $getIdStatement = $db->prepare ('SELECT player_id FROM players WHERE name_filelist=?');
    $getIdStatement->bindParam (1, $_POST['player'], \PDO::PARAM_STR);
    $getIdStatement->execute ();

    if ($getIdStatement->rowCount () !== 1) {
        die('player does not exist');
    } else {
        $pid = $getIdStatement->fetch (\PDO::FETCH_OBJ)->player_id;
    }

    $insertSt = $db->prepare ('INSERT INTO prizes (prize_id, player_id, prize, cost, date_bought, prize_type) ' .
                            'VALUES ' .
                            '(NULL, ?, ?, ?, ?, \'new\')');

    $insertSt->bindParam (1, $pid, \PDO::PARAM_INT);
    $insertSt->bindParam (2, $_POST['prize'], \PDO::PARAM_STR);
    $insertSt->bindParam (3, $_POST['cost'], \PDO::PARAM_INT);
    $insertSt->bindParam (4, $_POST['purchasedate'], \PDO::PARAM_STR);

    $insertSt->execute ();
    $rows = $insertSt->rowCount ();
} catch (\PDOException $e) {
    Logger::log('adding prize failed with $_POST = ' . print_r($_POST, true) . ': ' . $e->getMessage());
    header('Location: 500.shtml');
	exit();
}

echo "Added $rows prize(s)";