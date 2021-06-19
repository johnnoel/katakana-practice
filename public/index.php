<?php

use Doctrine\DBAL\DriverManager;

require __DIR__ . '/../vendor/autoload.php';

$db = DriverManager::getConnection([ 'url' => 'sqlite:///' . __DIR__ . '/../db.sqlite3' ]);
$sql = 'SELECT * FROM katakana ORDER BY RANDOM() LIMIT 1';
$res = $db->executeQuery($sql);
$row = $res->fetchAssociative();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- View the source code for this at https://github.com/johnnoel/katakana-practice -->
    <title>Katakana practice</title>
</head>
<body>
<h1><?= $row['katakana'] ?></h1>
<audio controls preload="none">
    <source type="audio/ogg" src="voice.php?id=<?= $row['id'] ?>">
</audio>
<button type="button" id="js-showcontrol">Show meaning</button>
<div id="js-show" style="display:none;">
    <dl>
        <dt>Meaning:</dt>
        <dd><?= $row['meaning'] ?></dd>

        <dt>Original line:</dt>
        <dd><?= $row['original'] ?></dd>

        <?php if ($row['extra1'] !== null): ?>
        <dt>Extra 1:</dt>
        <dd><?= $row['extra1'] ?></dd>
        <?php endif; ?>

        <?php if ($row['extra2'] !== null): ?>
            <dt>Extra 2:</dt>
            <dd><?= $row['extra2'] ?></dd>
        <?php endif; ?>

        <?php if ($row['extra3'] !== null): ?>
            <dt>Extra 3:</dt>
            <dd><?= $row['extra3'] ?></dd>
        <?php endif; ?>
    </dl>
</div>

<footer>
    Katakana words are extracted from the <a href="http://www.edrdg.org/wiki/index.php/JMdict-EDICT_Dictionary_Project">JMdict EDICT file</a> and used under the <a href="http://www.edrdg.org/edrdg/licence.html">Creative Commons Attribution-ShareAlike Licence (V3.0)</a>.
</footer>

<script>
    const btn = document.getElementById('js-showcontrol');
    btn.addEventListener('click', () => {
        document.getElementById('js-show').style.display = 'block';
    }, false);
</script>
</body>
</html>
