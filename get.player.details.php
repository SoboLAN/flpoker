<?php

require_once 'vendor/autoload.php';

use FileListPoker\Main\Config;
use FileListPoker\Main\Site;
use FileListPoker\Content\PlayerContent;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

//since this page is called via AJAX, this check is kept in order to maintain correct behaviour
if (! Config::getValue('online')) {
    $response = new Response('', Response::HTTP_SERVICE_UNAVAILABLE);
    $response->send();
    exit();
}

$site = new Site();

$errors = $site->isValidNumericQueryParameter('id', 4);
if (count($errors) > 0) {
    $response = new Response('', Response::HTTP_BAD_REQUEST);
    $response->send();
    exit();
}

$playerId = $site->request->query->get('id');

$content = new PlayerContent();

$details = $content->getGeneral($playerId);

if (count($details) == 0) {
    $response = new Response('', Response::HTTP_BAD_REQUEST);
    $response->send();
    exit();
}

$response = new JsonResponse($details, JsonResponse::HTTP_OK);
$response->send();
