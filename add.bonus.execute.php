<?php

require_once 'vendor/autoload.php';

use PDO as PDO;
use PDOException as PDOException;

use FileListPoker\Main\Site;
use FileListPoker\Main\Config;
use FileListPoker\Main\Database;

$site = new Site();

$adminPass = Config::getValue('admin_pass');

if (! isset($_POST['flpokerpassword']) || $_POST['flpokerpassword'] !== $adminPass) {
    die('nice try.');
}

if (! isset ($_POST['player']) ||
    ! isset ($_POST['tid']) ||
    ! isset ($_POST['bonusvalue']) ||
    ! isset ($_POST['bonusdate']) ||
    ! isset ($_POST['bonusdesc']))
{
    die('Some data is missing.');
}

$db = Database::getConnection();

$getIdStatement = $db->prepare('SELECT player_id FROM players WHERE name_pokerstars=?');
$getIdStatement->bindParam(1, $_POST['player'], PDO::PARAM_STR);
$getIdStatement->execute();

if ($getIdStatement->rowCount() !== 1) {
    die('player does not exist');
} else {
    $pid = $getIdStatement->fetch(PDO::FETCH_OBJ)->player_id;
}

$insertSt = $db->prepare(
    'INSERT INTO bonus_points (bonus_id, player_id, bonus_value, tournament_id, bonus_description, bonus_date) ' .
    'VALUES ' .
    '(NULL, ?, ?, ?, ?, ?)'
);

$insertSt->bindParam(1, $pid, PDO::PARAM_INT);
$insertSt->bindParam(2, $_POST['bonusvalue'], PDO::PARAM_INT);
$insertSt->bindParam(3, $_POST['tid'], PDO::PARAM_INT);
$insertSt->bindParam(4, $_POST['bonusdesc'], PDO::PARAM_STR);
$insertSt->bindParam(5, $_POST['bonusdate'], PDO::PARAM_STR);

$insertSt->execute();
$rows = $insertSt->rowCount();

echo "Added $rows bonus(es)";