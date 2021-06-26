<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210619152957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE words (
            id TEXT(36) NOT NULL,
            word TEXT(4096) NOT NULL,
            original TEXT(4096) NOT NULL,
            meaning TEXT(4096) NOT NULL,
            extra1 TEXT(4096),
            extra2 TEXT(4096),
            extra3 TEXT(4096),
            type TEXT(8) NOT NULL,
            CONSTRAINT words_PK PRIMARY KEY (id)
        )');

        $this->addSql('CREATE INDEX idx_words_type ON words (type)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE words');
    }
}
