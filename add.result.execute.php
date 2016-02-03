<?php

require_once 'vendor/autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Database;
use FileListPoker\Main\Config;
use FileListPoker\Content\TournamentsContent;
use FileListPoker\Main\TournamentStructureCalculator;

use Symfony\Component\HttpFoundation\Response;

$site = new Site();

$password = $site->request->request->get('flpokerpassword', null);

if ($password !== Config::getValue('admin_pass')) {
    $site->response->setContent('nice try');
    $site->response->setStatusCode(Response::HTTP_BAD_REQUEST);
    $site->response->send();
    exit();
}

$errors = $site->isValidNumericPostParameter('tournamentid', 4);
if (count($errors) > 0) {
    $message = 'Invalid tournament ID specified when acccessing add.result.execute.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
}

$tournamentId = $site->request->request->getInt('tournamentid');

$positions = array();
$i = 1;
while ($site->request->request->has('position' . $i, null)) {
    $positions[] = $site->request->request->getInt('position' . $i);
    $i++;
}

$structureCalculator = new TournamentStructureCalculator();
$filledPositions = $structureCalculator->getPayedPositions($positions);

$db = Database::getConnection();
$getIdStatement = $db->prepare('SELECT player_id FROM players WHERE name_pokerstars=?');
$insertResults = array();
$i = 1;
while ($site->request->request->has('player' . $i)) {
    $playerName = $site->request->request->get('player' . $i);

    $getIdStatement->bindParam(1, $playerName, PDO::PARAM_STR);
    $getIdStatement->execute();

    if ($getIdStatement->rowCount() !== 1) {
        break;
    } else {
        $getIdResult = $getIdStatement->fetch();
        $playerId = $getIdResult['player_id'];
    }

    $getIdStatement->closeCursor();

    $position = $positions[$i - 1];
    $points = $filledPositions[$position];
    $kos = $site->request->request->getInt('kos' . $i, null);

    $insertResults[] = array(
        'player' => $playerId,
        'points' => $points,
        'position' => $position,
        'kos' => $kos
    );

    $i++;
}

//var_dump($insertResults);
$tournamentsContent = new TournamentsContent();
$tournamentsContent->addTournamentResults($tournamentId, $insertResults);

$site->response->setContent(
    sprintf('Inserted %d rows for tournament %d', count($insertResults), $tournamentId)
);
$site->response->setStatusCode(Response::HTTP_OK);
$site->response->send();
