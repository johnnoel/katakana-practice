<?php

use Doctrine\DBAL\DriverManager;
use Ramsey\Uuid\Uuid;

require __DIR__ . '/vendor/autoload.php';

$db = DriverManager::getConnection([ 'url' => 'sqlite:///db.sqlite3' ]);
$sql = 'INSERT INTO words (id, word, original, meaning, extra1, extra2, extra3, type) VALUES (:id, :word, :original, :meaning, :extra1, :extra2, :extra3, :type)';
$stmt = $db->prepare($sql);

$learnedKanji = [];

//
// Get kanji I've learned from a mochi deck export
//
$mochiArchive = new ZipArchive();
$opened = $mochiArchive->open(__DIR__ . '/mochi-export.zip', ZipArchive::RDONLY);

if ($opened !== true) {
    echo $opened;
    exit(1);
}

for ($i = 0; $i < $mochiArchive->numFiles; $i++) {
    $stat = $mochiArchive->statIndex($i);

    if (substr($stat['name'], -3) !== '.md') {
        continue;
    }

    $markdownContent = $mochiArchive->getFromIndex($i);
    [ $markdownEnglish, $markdownKanji, $cardNo ] = preg_split('/\n---\n/u', $markdownContent, 3);

    $matches = [];
    preg_match('/# (.*)/u', $markdownKanji, $matches);

    $learnedKanji[] = $matches[1];
}

//
// Extract katakana-only words or match up a word to the kanji I've learned
//
$fh = fopen(__DIR__ . '/edict2', 'r');

while (!feof($fh)) {
    $line = trim(mb_convert_encoding(fgets($fh, 8192), 'UTF-8', 'EUC-JP'));

    if ($line === '') {
        continue;
    }

    $parts = preg_split('#/#u', $line);
    array_splice($parts, -2); // this should remove the blank ending and the entity ref

    $matches = [];
    preg_match('/^(.+?)(\s+\[(.+)\])?$/u', trim($parts[0]), $matches);

    $jParts = preg_split('/;/u', $matches[1]);
    $characters = preg_split('//u', $jParts[0], -1, PREG_SPLIT_NO_EMPTY);

    $isKatanaOnlyWord = preg_match('/^[\x{30A0}-\x{30FF}]+$/u', $jParts[0]) === 1;
    $isKanjiWord = (count($characters) >= 2 && count(array_diff($characters, $learnedKanji)) === 0);

    if (!$isKatanaOnlyWord && !$isKanjiWord) {
        continue;
    }

    $word = $jParts[0];
    $original = $parts[0];
    $meaning = $parts[1];
    $extra1 = (array_key_exists(2, $parts)) ? $parts[2] : null;
    $extra2 = (array_key_exists(3, $parts)) ? $parts[3] : null;
    $extra3 = (array_key_exists(4, $parts)) ? $parts[4] : null;
    $type = ($isKanjiWord) ? 'kanji' : 'katakana';

    $stmt->executeQuery([
        'id' => Uuid::uuid4()->toString(),
        'word' => $word,
        'original' => $original,
        'meaning' => $meaning,
        'extra1' => $extra1,
        'extra2' => $extra2,
        'extra3' => $extra3,
        'type' => $type,
    ]);

    echo '.';
}
