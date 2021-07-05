<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210606194322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Default values for tables role, character_class, message_type, game_version, region and faction';
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
                (1, 'Death Knight'),
                (2, 'Demon Hunter'),
                (3, 'Druid'),
                (4, 'Hunter'),
                (5, 'Mage'),
                (6, 'Monk'),
                (7, 'Paladin'),
                (8, 'Priest'),
                (9, 'Rogue'),
                (10, 'Shaman'),
                (11, 'Warlock'),
                (12, 'Warrior')
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
                (1, 'Retail'),
                (2, 'Classic'),
                (3, 'TBC Classic')
        ");

        $this->addSql("
            INSERT INTO region VALUES
                (1, 'Americas & Oceania'),
                (2, 'Europe'),
                (3, 'Korea'),
                (4, 'Taiwan')
        ");

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
