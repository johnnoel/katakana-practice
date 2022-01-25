<?php

use Aws\Credentials\Credentials;
use Aws\Polly\PollyClient;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

require __DIR__ . '/../vendor/autoload.php';

Dotenv::createImmutable(__DIR__ . '/../')->load();

$request = Request::createFromGlobals();
$response = new Response();

if (!$request->query->has('id')) {
    $response->setStatusCode(Response::HTTP_NOT_FOUND);
    $response->send();
    exit;
}

$id = $request->query->get('id');

$db = DriverManager::getConnection([ 'url' => 'sqlite:///' . __DIR__ . '/../db.sqlite3' ]);
$sql = 'SELECT * FROM words WHERE id = :id';
$res = $db->executeQuery($sql, [ 'id' => $id ]);
$row = $res->fetchAssociative();

if ($row === false) {
    $response->setStatusCode(Response::HTTP_NOT_FOUND);
    $response->send();
    exit;
}

$filePath = __DIR__ . '/../var/files/' . $row['id'] . '.ogg';

if (file_exists($filePath)) {
    $response = new BinaryFileResponse($filePath, Response::HTTP_OK, [
        'Content-Type' => 'audio/ogg',
    ]);
    $response->send();
    exit;
}

$credentials = new Credentials($_ENV['AWS_ACCESS_KEY_ID'], $_ENV['AWS_SECRET_ACCESS_KEY']);
$client = new PollyClient([
    'region' => 'eu-west-2',
    'version' => '2016-06-10',
    'credentials' => $credentials,
]);

$url = $client->createSynthesizeSpeechPreSignedUrl([
    'Engine' => 'standard',
    'LanguageCode' => 'ja-JP',
    'OutputFormat' => 'ogg_vorbis',
    'Text' => $row['word'],
    'TextType' => 'text',
    'VoiceId' => 'Mizuki',
]);

$written = file_put_contents($filePath, file_get_contents($url));
$response = new RedirectResponse($url);

if ($written !== false) {
    $response = new BinaryFileResponse($filePath, Response::HTTP_OK, [
        'Content-Type' => 'audio/ogg',
    ]);
}

$response->send();
exit;
