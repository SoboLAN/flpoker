<?php

require_once 'vendor/autoload.php';

use FileListPoker\Content\TournamentContent;
use FileListPoker\Main\Site;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Renderers\TournamentRenderer;
use FileListPoker\Renderers\FullPageRenderer;

use Symfony\Component\HttpFoundation\Response;

$site = new Site();

$errors = $site->isValidNumericQueryParameter('id', 3);
if (count($errors) > 0) {
    $message = 'Invalid tournament ID specified when acccessing tournament.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
}

$tid = $site->request->query->get('id');

$tournamentPage = new TournamentContent();

$details = $tournamentPage->getTournamentDetails($tid);
if (! isset($details['tournament_id'])) {
    $message = 'Non-existent tournament ID specified when acccessing tournament.php';
    throw new FLPokerException($message, FLPokerException::INVALID_REQUEST);
}

$results = $tournamentPage->getTournamentResults($tid);
$bonuses = $tournamentPage->getTournamentBonuses($tid);

$pageContent = file_get_contents('templates/tournament/tournament.tpl');

$renderer = new TournamentRenderer($site);

$detailsTpl = file_get_contents('templates/tournament/details.tpl');
$resultsTpl = file_get_contents('templates/tournament/results.tpl');
$bonusesTpl = file_get_contents('templates/tournament/bonuses.tpl');

$detailsTpl = $renderer->renderDetails($detailsTpl, $details);
$resultsTpl = $renderer->renderResults($resultsTpl, $results);
$bonusesTpl = $renderer->renderBonuses($bonusesTpl, $bonuses);

$pageContent = str_replace(
    array('{tournament_details}', '{tournament_results}', '{tournament_bonuses}'),
    array($detailsTpl, $resultsTpl, $bonusesTpl),
    $pageContent
);

$mainRenderer = new FullPageRenderer($site);
$htmlout = $mainRenderer->renderPage('tournaments.php');

$htmlout = str_replace(
    array('{content_type_id}', '{page_content}', '{bottom_page_scripts}'),
    array('content-narrower', $pageContent, ''),
    $htmlout
);

$site->response->setContent($htmlout);
$site->response->setStatusCode(Response::HTTP_OK);
$site->response->send();
