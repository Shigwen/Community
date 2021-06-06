<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210606203803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Server list';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            INSERT INTO server VALUES 
                (1, 'Amnennar', 'Europe/Berlin', NOW(), null),
                (2, 'Ashbringer', 'Europe/Berlin', NOW(), null),
                (3, 'Auberdine', 'Europe/Berlin', NOW(), null),
                (4, 'Bloodfang', 'Europe/Berlin', NOW(), null),
                (5, 'Celebras', 'Europe/Berlin', NOW(), null),
                (6, 'Chromie', 'Europe/Berlin', NOW(), null),
                (7, 'Dragon\'s Call', 'Europe/Berlin', NOW(), null),
                (8, 'Dragonfang', 'Europe/Berlin', NOW(), null),
                (9, 'Dreadmist', 'Europe/Berlin', NOW(), null),
                (10, 'Earthshaker', 'Europe/Berlin', NOW(), null),
                (11, 'Everlook', 'Europe/Berlin', NOW(), null),
                (12, 'Finkle', 'Europe/Berlin', NOW(), null),
                (13, 'Firemaw', 'Europe/Berlin', NOW(), null),
                (14, 'Flamegor', 'Europe/Berlin', NOW(), null),
                (15, 'Flamelash', 'Europe/Berlin', NOW(), null),
                (16, 'Gandling', 'Europe/Berlin', NOW(), null),
                (17, 'Gehennas', 'Europe/Berlin', NOW(), null),
                (18, 'Golemagg', 'Europe/Berlin', NOW(), null),
                (19, 'Harbinger of Doom', 'Europe/Berlin', NOW(), null),
                (20, 'Heartstriker', 'Europe/Berlin', NOW(), null),
                (21, 'Hydraxian Waterlords', 'Europe/Berlin', NOW(), null),
                (22, 'Judgement', 'Europe/Berlin', NOW(), null),
                (23, 'Lakeshire', 'Europe/Berlin', NOW(), null),
                (24, 'Lucifron', 'Europe/Berlin', NOW(), null),
                (25, 'Mandokir', 'Europe/Berlin', NOW(), null),
                (26, 'Mirage Raceway', 'Europe/Berlin', NOW(), null),
                (27, 'Mograine', 'Europe/Berlin', NOW(), null),
                (28, 'Nethergarde Keep', 'Europe/Berlin', NOW(), null),
                (29, 'Noggenfogger', 'Europe/Berlin', NOW(), null),
                (30, 'Patchwerk', 'Europe/Berlin', NOW(), null),
                (31, 'Pyrewood Village', 'Europe/Berlin', NOW(), null),
                (32, 'Razorfen', 'Europe/Berlin', NOW(), null),
                (33, 'Razorgore', 'Europe/Berlin', NOW(), null),
                (34, 'Rhok\'delar', 'Europe/Berlin', NOW(), null),
                (35, 'Shazzrah', 'Europe/Berlin', NOW(), null),
                (36, 'Skullflame', 'Europe/Berlin', NOW(), null),
                (37, 'Stonespine', 'Europe/Berlin', NOW(), null),
                (38, 'Sulfuron', 'Europe/Berlin', NOW(), null),
                (39, 'Ten Storms', 'Europe/Berlin', NOW(), null),
                (40, 'Transcendence', 'Europe/Berlin', NOW(), null),
                (41, 'Venoxis', 'Europe/Berlin', NOW(), null),
                (42, 'Wyrmthalak', 'Europe/Berlin', NOW(), null),
                (43, 'Zandalar Tribe', 'Europe/Berlin', NOW(), null)
        ");
    }

    public function down(Schema $schema): void
    {
        // no down
    }
}
