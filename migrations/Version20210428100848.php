<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210428100848 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raid ADD server_id INT NOT NULL');
        $this->addSql('ALTER TABLE raid ADD CONSTRAINT FK_578763B31844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
        $this->addSql('CREATE INDEX IDX_578763B31844E6B7 ON raid (server_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raid DROP FOREIGN KEY FK_578763B31844E6B7');
        $this->addSql('DROP INDEX IDX_578763B31844E6B7 ON raid');
        $this->addSql('ALTER TABLE raid DROP server_id');
    }
}
