<?php

require_once 'vendor/autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Database;
use FileListPoker\Main\CacheDB;
use FileListPoker\Main\Config;
use FileListPoker\Content\PlayerContent;

use PDO as PDO;

$site = new Site();

$adminPass = Config::getValue('admin_pass');

if (! isset($_POST['flpokerpassword']) || $_POST['flpokerpassword'] !== $adminPass) {
    die('nice try.');
}

if (! isset ($_POST['pname']) ||
    ! isset ($_POST['fname']) ||
    ! isset ($_POST['fid']) ||
    ! isset ($_POST['pid']))
{
    die('Some data is missing.');
}

$content = new PlayerContent();

$playerDetails = $content->getGeneral($_POST['pid']);

if (count($playerDetails) == 0) {
    die('Player with this ID does not exist (' . $_POST['pid'] . ')');
}

$db = Database::getConnection();

$updateQuery = 'UPDATE players SET ';
$binds = array();
$bindIndex = 1;
$edits = array();

if ($playerDetails['name_pokerstars'] != $_POST['pname']) {
    $updateQuery .= 'name_pokerstars = ?';
    $binds[] = array($bindIndex, $_POST['pname'], PDO::PARAM_STR);
    $bindIndex++;
    $edits[] = array('name' => 'PokerStars Name', 'old' => $playerDetails['name_pokerstars'], 'new' => $_POST['pname']);
}

if ($playerDetails['name_filelist'] != $_POST['fname']) {
    if ($bindIndex > 1) {
        $updateQuery .= ', ';
    }
    $updateQuery .= 'name_filelist = ?';
    $binds[] = array($bindIndex, $_POST['fname'], PDO::PARAM_STR);
    $bindIndex++;
    $edits[] = array('name' => 'FileList Name', 'old' => $playerDetails['name_filelist'], 'new' => $_POST['fname']);
}

if ($playerDetails['id_filelist'] != $_POST['fid']) {
    if ($bindIndex > 1) {
        $updateQuery .= ', ';
    }
    $updateQuery .= 'id_filelist = ?';
    $binds[] = array($bindIndex, $_POST['fid'], PDO::PARAM_INT);
    $bindIndex++;
    $edits[] = array('name' => 'FileList ID', 'old' => $playerDetails['id_filelist'], 'new' => $_POST['fid']);
}

if (empty($binds)) {
    die('Nothing to update');
}

$updateQuery .= ' WHERE player_id = ?';
$binds[] = array($bindIndex, $_POST['pid'], PDO::PARAM_INT);

$updateSt = $db->prepare($updateQuery);

foreach ($binds as $bind) {
    $updateSt->bindParam($bind[0], $bind[1], $bind[2]);
}

$updateSt->execute();

if ($updateSt->rowCount () !== 1) {
    die('There was an error while editing the player');
} elseif (Config::getValue('enable_cache')) {

    $cacheType = Config::getValue('cache_type');
    if ($cacheType == 'db') {
        $cache = new CacheDB();
    }

    if ($cache) {
        $key = Config::getValue('cache_key_player_general') . $_POST['pid'];
        $cache->flush($key);
    }
}

echo 'For the player with ID ' . $_POST['pid'] . ', the following information was changed: ';
echo '<ul>';
foreach ($edits as $edit) {
    echo '<li>';
    echo $edit['name'] . ' was changed from ' . $edit['old'] . ' to ' . $edit['new'];
    echo '</li>';
}
echo '</ul>';