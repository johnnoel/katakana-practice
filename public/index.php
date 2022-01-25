<?php
use Doctrine\DBAL\DriverManager;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$db = DriverManager::getConnection([ 'url' => 'sqlite:///' . __DIR__ . '/../db.sqlite3' ]);
$sql = 'SELECT * FROM words ORDER BY RANDOM() LIMIT 1';
$res = $db->executeQuery($sql);
$row = $res->fetchAssociative();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- View the source code for this at https://github.com/johnnoel/katakana-practice -->
    <title>Japanese word practice</title>

    <style>
        h1 { font-size: 14px; }
        h2 { font-size: 64px; }
        audio { margin: 0 0 1em; }
        button { font-size: 1.5em; margin: 0 0 1em; }
    </style>
</head>
<body>
<main role="main">
    <h1>Katakana practice</h1>
    <h2><?php echo $row['word']; ?></h2>

    <div>
        <audio controls preload="none">
            <source type="audio/ogg" src="voice.php?id=<?php echo $row['id']; ?>">
        </audio>
    </div>

    <button type="button" id="js-togglemeaning">Toggle word meaning</button>

    <div id="js-meaning" style="display:none">
        <dl>
            <dt>Meaning:</dt>
            <dd><?php echo $row['meaning']; ?></dd>

            <dt>Original line:</dt>
            <dd><?php echo $row['original']; ?></dd>

            <?php if ($row['extra1']): ?>
            <dt>Extra 1:</dt>
            <dd><?php echo $row['extra1']; ?></dd>
            <?php endif; ?>

            <?php if ($row['extra2']): ?>
            <dt>Extra 2:</dt>
            <dd><?php echo $row['extra2']; ?></dd>
            <?php endif; ?>

            <?php if ($row['extra3']): ?>
            <dt>Extra 3:</dt>
            <dd><?php echo $row['extra3']; ?></dd>
            <?php endif; ?>
        </dl>
    </div>

    <div>
        <button type="button" id="js-refresh">New word</button>
    </div>
</main>

<footer>
    <small>Words are extracted from the <a href="http://www.edrdg.org/wiki/index.php/JMdict-EDICT_Dictionary_Project">JMdict EDICT file</a> and used under the <a href="http://www.edrdg.org/edrdg/licence.html">Creative Commons Attribution-ShareAlike Licence (V3.0)</a>.</small>
</footer>

<script async defer>
    const refresh = document.getElementById('js-refresh');
    refresh.addEventListener('click', () => window.location.reload(), false);

    const meaning = document.getElementById('js-meaning');
    const toggleMeaning = document.getElementById('js-togglemeaning');
    toggleMeaning.addEventListener('click', () => meaning.style.display = (meaning.style.display === 'block') ? 'none' : 'block');
</script>
</body>
</html>
