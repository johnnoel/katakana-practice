# Japanese words practice

[Live demo](https://katakana.johnnoel.uk)

Practice katakana and kanji words extracted from [JMdict EDICT](http://www.edrdg.org/wiki/index.php/JMdict-EDICT_Dictionary_Project).

## Up and running

You'll need [PHP 8.0](https://www.php.net/) and [Composer](https://getcomposer.org/).

1. Clone this repository
2. Run `composer install`  
3. Download the JMdict EDICT2 file and extract to the same directory `[extract.php](extract.php)`
4. Run `vendor/bin/doctrine-migrations migrate` which will will create the `db.sqlite3` file
5. Run `php extract.php` to pull out katakana words from the EDICT file and pop them in to SQLite database
6. Copy `.env.dist` to `.env` and fill in the AWS credentials
7. Copy everything to a web-accessible host
8. Done

## Reasoning

I needed some practice with katakana words - Duolingo is great but easy to guess from context - and wanted to hear what the word should sound like and understand what it's supposed to say / mean.

## Build

Uses the wonderful JMdict project and pulls out words that are totally within the [Unicode katakana block](https://en.wikipedia.org/wiki/Katakana_(Unicode_block)), stores them within an SQLite database, and then pulls out a random one on each page load.

The voice is generated by [Amazon Polly](https://aws.amazon.com/polly/) and is played on-demand (as long as your browser doesn't autoload audio that is flagged with `preload="none"`) via a pre-signed URL.

## Attributions

The JMDict EDICT file is used under the [Creative Commons Attribution-ShareAlike Licence (V3.0)](http://www.edrdg.org/edrdg/licence.html).
