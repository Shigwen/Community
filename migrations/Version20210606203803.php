<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210606203803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Europe server list for Burning crusade classic with timezone Europe/Berlin';
    }

    public function up(Schema $schema): void
    {
        // VALUES (id, GameVersion, Region, Timezone, ServerName)
        // GameVersion id 3 => Burning crusade classic 
        // Region id 2 => Europe
        // Timezone id 1 => Europe/Berlin

        $this->addSql("
            INSERT INTO server VALUES 
                (1,  3, 2 , 1, 'Amnennar'),
                (2,  3, 2 , 1, 'Ashbringer'),
                (3,  3, 2 , 1, 'Auberdine'),
                (4,  3, 2 , 1, 'Bloodfang'),
                (5,  3, 2 , 1, 'Celebras'),
                (6,  3, 2 , 1, 'Chromie'),
                (7,  3, 2 , 1, 'Dragon\'s Call'),
                (8,  3, 2 , 1, 'Dragonfang'),
                (9,  3, 2 , 1, 'Dreadmist'),
                (10, 3, 2 , 1, 'Earthshaker'),
                (11, 3, 2 , 1, 'Everlook'),
                (12, 3, 2 , 1, 'Finkle'),
                (13, 3, 2 , 1, 'Firemaw'),
                (14, 3, 2 , 1, 'Flamegor'),
                (15, 3, 2 , 1, 'Flamelash'),
                (16, 3, 2 , 1, 'Gandling'),
                (17, 3, 2 , 1, 'Gehennas'),
                (18, 3, 2 , 1, 'Golemagg'),
                (19, 3, 2 , 1, 'Harbinger of Doom'),
                (20, 3, 2 , 1, 'Heartstriker'),
                (21, 3, 2 , 1, 'Hydraxian Waterlords'),
                (22, 3, 2 , 1, 'Judgement'),
                (23, 3, 2 , 1, 'Lakeshire'),
                (24, 3, 2 , 1, 'Lucifron'),
                (25, 3, 2 , 1, 'Mandokir'),
                (26, 3, 2 , 1, 'Mirage Raceway'),
                (27, 3, 2 , 1, 'Mograine'),
                (28, 3, 2 , 1, 'Nethergarde Keep'),
                (29, 3, 2 , 1, 'Noggenfogger'),
                (30, 3, 2 , 1, 'Patchwerk'),
                (31, 3, 2 , 1, 'Pyrewood Village'),
                (32, 3, 2 , 1, 'Razorfen'),
                (33, 3, 2 , 1, 'Razorgore'),
                (34, 3, 2 , 1, 'Rhok\'delar'),
                (35, 3, 2 , 1, 'Shazzrah'),
                (36, 3, 2 , 1, 'Skullflame'),
                (37, 3, 2 , 1, 'Stonespine'),
                (38, 3, 2 , 1, 'Sulfuron'),
                (39, 3, 2 , 1, 'Ten Storms'),
                (40, 3, 2 , 1, 'Transcendence'),
                (41, 3, 2 , 1, 'Venoxis'),
                (42, 3, 2 , 1, 'Wyrmthalak'),
                (43, 3, 2 , 1, 'Zandalar Tribe')
        ");
    }

    public function down(Schema $schema): void
    {
        // no down
    }
}
