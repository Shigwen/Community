<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419092830 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add raid identifier and auto accept in Raid table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raid ADD identifier VARCHAR(20) NOT NULL AFTER id, ADD auto_accept TINYINT(1) NOT NULL AFTER max_heal;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raid DROP identifier, DROP auto_accept;');
    }
}
