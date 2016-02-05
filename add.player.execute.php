<?php

require_once 'vendor/autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Database;
use FileListPoker\Main\Config;

use PDO as PDO;

$site = new Site();

$adminPass = Config::getValue('admin_pass');

if (! isset($_POST['flpokerpassword']) || $_POST['flpokerpassword'] !== $adminPass) {
    die('nice try.');
}

if (! isset ($_POST['registrationdate']) ||
    ! isset ($_POST['nameps']) ||
    ! isset ($_POST['namefl']) ||
    ! isset ($_POST['idfl']))
{
    die('Some data is missing.');
}

$db = Database::getConnection();

$getSt = $db->prepare('SELECT 1 FROM players WHERE name_pokerstars=? OR name_filelist=? OR id_filelist=?');

$getSt->bindParam(1, $_POST['nameps'], PDO::PARAM_STR);
$getSt->bindParam(2, $_POST['namefl'], PDO::PARAM_STR);
$getSt->bindParam(3, $_POST['idfl'], PDO::PARAM_INT);

$getSt->execute();

if ($getSt->rowCount() > 0) {
    die('This PokerStars name, FileList name or FileList ID already exist in the database');
}

$insertSt = $db->prepare(
    'INSERT INTO players (player_id, name_pokerstars, name_filelist, id_filelist, member_type, ' .
    'initial_accumulated_points, initial_spent_points, join_date) ' .
    'VALUES ' .
    '(NULL, ?, ?, ?, \'regular\', 0, 0, ?)'
);

$insertSt->bindParam(1, $_POST['nameps'], PDO::PARAM_STR);
$insertSt->bindParam(2, $_POST['namefl'], PDO::PARAM_STR);
$insertSt->bindParam(3, $_POST['idfl'], PDO::PARAM_INT);
$insertSt->bindParam(4, $_POST['registrationdate'], PDO::PARAM_STR);

$insertSt->execute();

if ($insertSt->rowCount() !== 1) {
    die('There was an error while adding the player');
}

$getIdStatement = $db->prepare('SELECT MAX(player_id) AS player_id FROM players');
$getIdStatement->execute();

$id = $getIdStatement->fetch(PDO::FETCH_OBJ)->player_id;

echo "Added player with ID $id ({$_POST['nameps']}).";
