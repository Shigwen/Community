<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210610195134 extends AbstractMigration
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
        // Timezone => Europe/Berlin

        if (!$timezone = $this->connection->executeQuery("SELECT `id` FROM timezone WHERE `name` = 'Europe/Berlin'")->fetchOne()) {
            echo 'Pas de timezone trouvé pour Europe/Berlin';
            return;
        }

        $this->addSql("
            INSERT INTO server VALUES
                (1,  3, 2 , $timezone, 'Amnennar'),
                (2,  3, 2 , $timezone, 'Ashbringer'),
                (3,  3, 2 , $timezone, 'Auberdine'),
                (4,  3, 2 , $timezone, 'Bloodfang'),
                (5,  3, 2 , $timezone, 'Celebras'),
                (6,  3, 2 , $timezone, 'Chromie'),
                (7,  3, 2 , $timezone, 'Dragon\'s Call'),
                (8,  3, 2 , $timezone, 'Dragonfang'),
                (9,  3, 2 , $timezone, 'Dreadmist'),
                (10, 3, 2 , $timezone, 'Earthshaker'),
                (11, 3, 2 , $timezone, 'Everlook'),
                (12, 3, 2 , $timezone, 'Finkle'),
                (13, 3, 2 , $timezone, 'Firemaw'),
                (14, 3, 2 , $timezone, 'Flamegor'),
                (15, 3, 2 , $timezone, 'Flamelash'),
                (16, 3, 2 , $timezone, 'Gandling'),
                (17, 3, 2 , $timezone, 'Gehennas'),
                (18, 3, 2 , $timezone, 'Golemagg'),
                (19, 3, 2 , $timezone, 'Harbinger of Doom'),
                (20, 3, 2 , $timezone, 'Heartstriker'),
                (21, 3, 2 , $timezone, 'Hydraxian Waterlords'),
                (22, 3, 2 , $timezone, 'Judgement'),
                (23, 3, 2 , $timezone, 'Lakeshire'),
                (24, 3, 2 , $timezone, 'Lucifron'),
                (25, 3, 2 , $timezone, 'Mandokir'),
                (26, 3, 2 , $timezone, 'Mirage Raceway'),
                (27, 3, 2 , $timezone, 'Mograine'),
                (28, 3, 2 , $timezone, 'Nethergarde Keep'),
                (29, 3, 2 , $timezone, 'Noggenfogger'),
                (30, 3, 2 , $timezone, 'Patchwerk'),
                (31, 3, 2 , $timezone, 'Pyrewood Village'),
                (32, 3, 2 , $timezone, 'Razorfen'),
                (33, 3, 2 , $timezone, 'Razorgore'),
                (34, 3, 2 , $timezone, 'Rhok\'delar'),
                (35, 3, 2 , $timezone, 'Shazzrah'),
                (36, 3, 2 , $timezone, 'Skullflame'),
                (37, 3, 2 , $timezone, 'Stonespine'),
                (38, 3, 2 , $timezone, 'Sulfuron'),
                (39, 3, 2 , $timezone, 'Ten Storms'),
                (40, 3, 2 , $timezone, 'Transcendence'),
                (41, 3, 2 , $timezone, 'Venoxis'),
                (42, 3, 2 , $timezone, 'Wyrmthalak'),
                (43, 3, 2 , $timezone, 'Zandalar Tribe')
        ");
    }

    public function down(Schema $schema): void
    {
        // no down
    }
}
