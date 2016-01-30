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

$db = Database::getConnection();

$i = 1;
$getIdStatement = $db->prepare('SELECT player_id FROM players WHERE name_pokerstars=?');
$insertResults = array();
$tid = $_POST['tournamentid'];
while (true) {
    if (! isset($_POST["player$i"]) || empty($_POST["player$i"])) {
        break;
    }

    if ($_POST["player$i"] == 'NULL') {
        $id = 'NULL';
    } else {
        $getIdStatement->bindParam(1, $_POST["player$i"], PDO::PARAM_STR);
        $getIdStatement->execute();

        if ($getIdStatement->rowCount() !== 1) {
            break;
        } else {
            $id = $getIdStatement->fetch(PDO::FETCH_OBJ)->player_id;
        }

        $getIdStatement->closeCursor();
    }

    $points = $_POST['points' . $i];
    $position = $_POST['position' . $i];
    $kos = $_POST['kos' . $i];

    $insertResults[] = "($id, $tid, $points, $position, $kos)";

    $i++;
}

$result = $db->exec(
    'INSERT INTO results(player_id, tournament_id, points, position, kos) ' .
    'VALUES ' .
    implode (',', $insertResults)
);

echo "Inserted $result rows.";
