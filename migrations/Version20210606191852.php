<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210606191852 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `character` ADD faction VARCHAR(8) NOT NULL, CHANGE is_archived is_archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE raid CHANGE max_tank max_tank SMALLINT NOT NULL, CHANGE max_heal max_heal SMALLINT NOT NULL, CHANGE is_archived is_archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE server ADD timezone VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `character` DROP faction, CHANGE is_archived is_archived TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE raid CHANGE max_tank max_tank SMALLINT DEFAULT NULL, CHANGE max_heal max_heal SMALLINT DEFAULT NULL, CHANGE is_archived is_archived TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE server DROP timezone');
    }
}
