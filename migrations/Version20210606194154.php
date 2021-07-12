<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210606194154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create database';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `character` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, character_class_id INT NOT NULL, server_id INT NOT NULL, faction_id INT NOT NULL, name VARCHAR(255) NOT NULL, information LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_archived TINYINT(1) NOT NULL, INDEX IDX_937AB034A76ED395 (user_id), INDEX IDX_937AB034B201E281 (character_class_id), INDEX IDX_937AB0341844E6B7 (server_id), INDEX IDX_937AB0344448F8DA (faction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE character_role (character_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_40959EF21136BE75 (character_id), INDEX IDX_40959EF2D60322AC (role_id), PRIMARY KEY(character_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE character_class (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faction (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(8) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_version (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, message_type_id INT NOT NULL, archived_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, archived_at DATETIME DEFAULT NULL, INDEX IDX_B6BD307F55C4B69F (message_type_id), INDEX IDX_B6BD307F77BE2925 (archived_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE raid (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, identifier VARCHAR(20) DEFAULT NULL, name VARCHAR(255) NOT NULL, template_name VARCHAR(255) DEFAULT NULL, raid_type SMALLINT NOT NULL, expected_attendee SMALLINT NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, information LONGTEXT DEFAULT NULL, min_tank SMALLINT NOT NULL, max_tank SMALLINT NOT NULL, min_heal SMALLINT NOT NULL, max_heal SMALLINT NOT NULL, auto_accept TINYINT(1) NOT NULL, is_private TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_archived TINYINT(1) NOT NULL, INDEX IDX_578763B3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE raid_character (id INT AUTO_INCREMENT NOT NULL, raid_id INT NOT NULL, user_character_id INT NOT NULL, role_id INT NOT NULL, status SMALLINT NOT NULL, INDEX IDX_6145C7889C55ABC9 (raid_id), INDEX IDX_6145C78891FAC277 (user_character_id), INDEX IDX_6145C788D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE server (id INT AUTO_INCREMENT NOT NULL, game_version_id INT NOT NULL, region_id INT NOT NULL, timezone_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_5A6DD5F6A560E0E8 (game_version_id), INDEX IDX_5A6DD5F698260155 (region_id), INDEX IDX_5A6DD5F63FE997DE (timezone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timezone (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, timezone_id INT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, nbr_of_attempt SMALLINT NOT NULL, last_attempt DATETIME NOT NULL, token VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D6495E237E06 (name), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6493FE997DE (timezone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB034A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB034B201E281 FOREIGN KEY (character_class_id) REFERENCES character_class (id)');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB0341844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB0344448F8DA FOREIGN KEY (faction_id) REFERENCES faction (id)');
        $this->addSql('ALTER TABLE character_role ADD CONSTRAINT FK_40959EF21136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_role ADD CONSTRAINT FK_40959EF2D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F55C4B69F FOREIGN KEY (message_type_id) REFERENCES message_type (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F77BE2925 FOREIGN KEY (archived_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE raid ADD CONSTRAINT FK_578763B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE raid_character ADD CONSTRAINT FK_6145C7889C55ABC9 FOREIGN KEY (raid_id) REFERENCES raid (id)');
        $this->addSql('ALTER TABLE raid_character ADD CONSTRAINT FK_6145C78891FAC277 FOREIGN KEY (user_character_id) REFERENCES `character` (id)');
        $this->addSql('ALTER TABLE raid_character ADD CONSTRAINT FK_6145C788D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F6A560E0E8 FOREIGN KEY (game_version_id) REFERENCES game_version (id)');
        $this->addSql('ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F698260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F63FE997DE FOREIGN KEY (timezone_id) REFERENCES timezone (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493FE997DE FOREIGN KEY (timezone_id) REFERENCES timezone (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE raid_character ADD created_at DATETIME NOT NULL;');
    }

    public function down(Schema $schema): void
    {
        // no down
    }
}
