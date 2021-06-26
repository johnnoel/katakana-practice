<?php

use Doctrine\DBAL\DriverManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();
$type = $request->query->get('type', 'katakana');

if (!in_array($type, [ 'katakana', 'kanji' ])) {
    $response = new Response();
    $response->setStatusCode(Response::HTTP_NOT_FOUND);
    $response->send();
    exit;
}

$db = DriverManager::getConnection([ 'url' => 'sqlite:///' . __DIR__ . '/../db.sqlite3' ]);
$sql = 'SELECT * FROM words WHERE type = ? ORDER BY RANDOM() LIMIT 1';
$res = $db->executeQuery($sql, [ $type ]);
$row = $res->fetchAssociative();

$response = new JsonResponse($row);
$response->send();
exit;
