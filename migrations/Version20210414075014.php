<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414075014 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create all table (except User) with index and relation (foreign key)';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `character` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, character_class_id INT NOT NULL, server_id INT NOT NULL, information LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_937AB034A76ED395 (user_id), INDEX IDX_937AB034B201E281 (character_class_id), INDEX IDX_937AB0341844E6B7 (server_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE character_role (character_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_40959EF21136BE75 (character_id), INDEX IDX_40959EF2D60322AC (role_id), PRIMARY KEY(character_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE character_class (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ip (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ip VARCHAR(50) NOT NULL, INDEX IDX_A5E3B32DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE raid (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, raid_type SMALLINT NOT NULL, expected_attendee SMALLINT NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, information LONGTEXT NOT NULL, min_tank SMALLINT NOT NULL, max_tank SMALLINT DEFAULT NULL, min_heal SMALLINT NOT NULL, max_heal SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_578763B3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE raid_character (id INT AUTO_INCREMENT NOT NULL, raid_id INT NOT NULL, user_character_id INT NOT NULL, role_id INT NOT NULL, status SMALLINT NOT NULL, INDEX IDX_6145C7889C55ABC9 (raid_id), INDEX IDX_6145C78891FAC277 (user_character_id), INDEX IDX_6145C788D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE raid_template (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, raid_type SMALLINT NOT NULL, expected_attendee SMALLINT NOT NULL, day_of_week SMALLINT NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, information LONGTEXT NOT NULL, min_tank SMALLINT NOT NULL, max_tank SMALLINT DEFAULT NULL, min_heal SMALLINT NOT NULL, max_heal SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_CA17EDD0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(5) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE server (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB034A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB034B201E281 FOREIGN KEY (character_class_id) REFERENCES character_class (id)');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB0341844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
        $this->addSql('ALTER TABLE character_role ADD CONSTRAINT FK_40959EF21136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_role ADD CONSTRAINT FK_40959EF2D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ip ADD CONSTRAINT FK_A5E3B32DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE raid ADD CONSTRAINT FK_578763B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE raid_character ADD CONSTRAINT FK_6145C7889C55ABC9 FOREIGN KEY (raid_id) REFERENCES raid (id)');
        $this->addSql('ALTER TABLE raid_character ADD CONSTRAINT FK_6145C78891FAC277 FOREIGN KEY (user_character_id) REFERENCES `character` (id)');
        $this->addSql('ALTER TABLE raid_character ADD CONSTRAINT FK_6145C788D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE raid_template ADD CONSTRAINT FK_CA17EDD0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD status SMALLINT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE character_role DROP FOREIGN KEY FK_40959EF21136BE75');
        $this->addSql('ALTER TABLE raid_character DROP FOREIGN KEY FK_6145C78891FAC277');
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB034B201E281');
        $this->addSql('ALTER TABLE raid_character DROP FOREIGN KEY FK_6145C7889C55ABC9');
        $this->addSql('ALTER TABLE character_role DROP FOREIGN KEY FK_40959EF2D60322AC');
        $this->addSql('ALTER TABLE raid_character DROP FOREIGN KEY FK_6145C788D60322AC');
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB0341844E6B7');
        $this->addSql('DROP TABLE `character`');
        $this->addSql('DROP TABLE character_role');
        $this->addSql('DROP TABLE character_class');
        $this->addSql('DROP TABLE ip');
        $this->addSql('DROP TABLE raid');
        $this->addSql('DROP TABLE raid_character');
        $this->addSql('DROP TABLE raid_template');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE server');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('ALTER TABLE user DROP status');
    }
}
