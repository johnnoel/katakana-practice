<?php

use Doctrine\DBAL\DriverManager;
use Ramsey\Uuid\Uuid;

require __DIR__ . '/vendor/autoload.php';

$db = DriverManager::getConnection([ 'url' => 'sqlite:///db.sqlite3' ]);
$sql = 'INSERT INTO words (id, word, original, meaning, extra1, extra2, extra3) VALUES (:id, :word, :original, :meaning, :extra1, :extra2, :extra3)';
$stmt = $db->prepare($sql);

//
// Extract katakana-only words
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

    if (!$isKatanaOnlyWord) {
        continue;
    }

    $word = $jParts[0];
    $original = $parts[0];
    $meaning = $parts[1];
    $extra1 = (array_key_exists(2, $parts)) ? $parts[2] : null;
    $extra2 = (array_key_exists(3, $parts)) ? $parts[3] : null;
    $extra3 = (array_key_exists(4, $parts)) ? $parts[4] : null;

    $stmt->executeQuery([
        'id' => Uuid::uuid4()->toString(),
        'word' => $word,
        'original' => $original,
        'meaning' => $meaning,
        'extra1' => $extra1,
        'extra2' => $extra2,
        'extra3' => $extra3,
    ]);

    echo '.';
}
