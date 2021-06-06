<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20210606194322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Default values for tables role, character_class and message_type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            INSERT INTO role VALUES 
                (1, 'Tank', NOW(), null),
                (2, 'Heal', NOW(), null),
                (3, 'DPS', NOW(), null)
        ");

        $this->addSql("
            INSERT INTO character_class VALUES 
                (1, 'Druid', NOW(), null),
                (2, 'Hunter', NOW(), null),
                (3, 'Mage', NOW(), null),
                (4, 'Paladin', NOW(), null),
                (5, 'Priest', NOW(), null),
                (6, 'Rogue', NOW(), null),
                (7, 'Shaman', NOW(), null),
                (8, 'Warlock', NOW(), null),
                (9, 'Warrior', NOW(), null)
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
    }

    public function down(Schema $schema): void
    {
        // no down
    }
}
