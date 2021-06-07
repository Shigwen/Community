<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210606194322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Default values for tables role, character_class, message_type, game_version, region, timezone and faction';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            INSERT INTO role VALUES 
                (1, 'Tank'),
                (2, 'Heal'),
                (3, 'DPS')
        ");

        $this->addSql("
            INSERT INTO character_class VALUES 
                (1, 'Druid'),
                (2, 'Hunter'),
                (3, 'Mage'),
                (4, 'Paladin'),
                (5, 'Priest'),
                (6, 'Rogue'),
                (7, 'Shaman'),
                (8, 'Warlock'),
                (9, 'Warrior')
        ");

        $this->addSql("
            INSERT INTO message_type VALUES 
                (1, 'I\'d like to become a Raid Leader and organize my own events'),
                (2, 'I cannot sign up / sign in'),
                (3, 'I cannot create/modify characters'),
                (4, 'I cannot create/modify a raid'),
                (5, 'I cannot subscribe to / unsubscribe from a raid'),
                (6, 'I was banned from the website'),
                (7, 'Others')
        ");

        $this->addSql("
            INSERT INTO game_version VALUES 
                (1, 'World of Warcraft'),
                (2, 'World of Warcraft Classic'),
                (3, 'Burning Crusade Classic')
        ");

        $this->addSql("
            INSERT INTO region VALUES 
                (1, 'Americas & Oceania'),
                (2, 'Europe'),
                (3, 'Korea'),
                (4, 'Taiwan')
        ");

        $this->addSql("INSERT INTO timezone VALUES (1, 'Europe/Berlin')");

        $this->addSql("
            INSERT INTO faction VALUES 
                (1, 'Alliance'),
                (2, 'Horde')
        ");
    }

    public function down(Schema $schema): void
    {
        // no down
    }
}
