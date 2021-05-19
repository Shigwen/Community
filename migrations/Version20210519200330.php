<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519200330 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add field archivedBy in the table Message';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD archived_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F77BE2925 FOREIGN KEY (archived_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F77BE2925 ON message (archived_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F77BE2925');
        $this->addSql('DROP INDEX IDX_B6BD307F77BE2925 ON message');
        $this->addSql('ALTER TABLE message DROP archived_by_id');
    }
}
