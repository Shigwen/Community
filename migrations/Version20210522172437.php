<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210522172437 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add default role user';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("
            INSERT INTO role VALUES 
                (1, 'Tank', NOW(), null),
                (2, 'Heal', NOW(), null),
                (3, 'DPS', NOW(), null)
        ");

        $this->addSql("
            INSERT INTO character_class VALUES 
                (1, 'Druid', NOW(), null)
                (2, 'Hunter', NOW(), null)
                (3, 'Mage', NOW(), null)
                (4, 'Paladin', NOW(), null),
                (5, 'Priest', NOW(), null)
                (6, 'Rogue', NOW(), null)
                (7, 'Shaman', NOW(), null)
                (8, 'Warlock', NOW(), null)
                (9, 'Warrior', NOW(), null),
        ");

        $this->addSql("
            INSERT INTO message_type VALUES 
                (1, 'Je souhaite pouvoir créer mes propres raids'),
                (2, 'Je n\'arrive pas à m\'inscrire/me connecter sur le site'),
                (3, 'Je n\'arrive pas à créer/modifier un personnage'),
                (4, 'Je n\'arrive pas à créer/modifier un raid'),
                (5, 'Je n\'arrive pas à m\'inscrire/me désinscrire à un raid'),
                (6, 'J\'ai été banni du site'),
                (7, 'Autre'),
        ");
    }

    public function down(Schema $schema) : void
    {
        // no down
    }
}
