<?php

require_once 'vendor/autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Config;
use FileListPoker\Main\Dictionary;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

$site = new Site();

$lang = $site->request->query->get('lang');

//if language is not OK, go away
if (! Dictionary::isValidLanguage($lang)) {
    $site->response->headers->set('Location', 'index.php');
    $site->response->send();
    exit();
}

$cookie = new Cookie(
    Config::getValue('lang_cookie_name'),
    $lang,
    time() + Config::getValue('lang_cookie_duration')
);

$site->response->headers->setCookie($cookie);

//figure out where the user is so you can send him back to the same page
$sp = strtolower($site->request->server->get('SERVER_PROTOCOL'));
$https = $site->request->server->get('HTTPS');
$host = $site->request->server->get('HTTP_HOST');
$returnPage = $site->request->query->get('returnpage');

$protocol = substr($sp, 0, strpos($sp, '/')) . (! empty($https) && $https == 'on' ? 's' : '');
$redirPage = $protocol . '://' . $host . $returnPage;

//redirect the user
$site->response->setStatusCode(Response::HTTP_FOUND);
$site->response->headers->set('Location', $redirPage);
$site->response->send();