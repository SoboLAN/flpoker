<?php

require_once 'autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Config;
use FileListPoker\Main\Dictionary;

$site = new Site();

//if language is not OK, go away
if (! Dictionary::isValidLanguage($_GET['lang'])) {
    header('Location: index.php');
}

//language setting is stored in a cookie. so we need parameters for it
$cookieName = Config::getValue('lang_cookie_name');
$cookieDuration = Config::getValue('lang_cookie_duration');

//set the new language in the cookie
setcookie($cookieName, $_GET['lang'], time() + $cookieDuration);

//figure out where the user is so you can send him back to the same page
$sp = strtolower($_SERVER['SERVER_PROTOCOL']);
$protocol = substr($sp, 0, strpos($sp, '/')) . (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '');
$redirpage = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_GET['returnpage'];

//redirect the user
header("Location: $redirpage");