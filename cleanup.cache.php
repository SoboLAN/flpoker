<?php

require_once 'autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Database;

$site = new Site();

$db = Database::getConnection();

//delete expired cache entries
$result = $db->query('DELETE FROM cache WHERE entry_time + lifetime < UNIX_TIMESTAMP(NOW())');
