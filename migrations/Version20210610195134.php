<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210610195134 extends AbstractMigration
{
    public $serversClassic = [
        'Amnennar', 'Ashbringer', 'Auberdine', 'Bloodfang', 'Celebras', 'Dragonfang', 'Dreadmist', 'Earthshaker', 'Ewige Warte', 'Finkle', 'Firemaw', 'Flamelash', 'Flickwerk', 'Gandling', 'Gehennas', 'Golemagg', 'Großdrachenruf', 'Herzensbrecher', 'Hydraxian Waterlords', 'Judgement', 'Klingenhauer', 'Lucifron', 'Mandokir', 'Mirage Raceway', 'Mograine', 'Nethergarde Keep', 'Noggenfogger', 'Pyrewood Village', 'Razorgore', 'Seenhain', 'Shazzrah', 'Skullflame', 'Stonespine', 'Sulfuron', 'Ten Storms', 'Tranzendenz', 'Venoxis', 'Zandalar Tribe	JdR', 'Вестник Рока', 'Змейталак', 'Пламегор', 'Рок-Делар', 'Хроми',
    ];

    public $serversTBC = [
        'Amnennar', 'Ashbringer', 'Auberdine', 'Bloodfang', 'Celebras', 'Chromie', 'Dragon\'s Call', 'Dragonfang', 'Dreadmist', 'Earthshaker', 'Everlook', 'Finkle', 'Firemaw', 'Flamegor', 'Flamelash', 'Gandling', 'Gehennas', 'Golemagg', 'Harbinger of Doom', 'Heartstriker', 'Hydraxian Waterlords', 'Judgement', 'Lakeshire', 'Lucifron', 'Mandokir', 'Mirage Raceway', 'Mograine', 'Nethergarde Keep', 'Noggenfogger', 'Patchwerk', 'Pyrewood Village', 'Razorfen', 'Razorgore', 'Rhok\'delar', 'Shazzrah', 'Skullflame', 'Stonespine', 'Sulfuron', 'Ten Storms', 'Transcendence', 'Venoxis', 'Wyrmthalak', 'Zandalar Tribe',
    ];

    public $serversRetail = [
        'Aegwynn', 'Aerie Peak', 'Agamaggan', 'Aggra (Português)', 'Aggramar', 'Ahn\'Qiraj', 'Al\'Akir', 'Alexstrasza', 'Alleria', 'Alonsus', 'Aman\'Thul', 'Ambossar', 'Anachronos', 'Anetheron', 'Antonidas', 'Anub\'arak', 'Arak-arahm', 'Arathi', 'Arathor', 'Archimonde', 'Area 52', 'Argent Dawn', 'Arthas', 'Arygos', 'Ashenvale', 'Aszune', 'Auchindoun', 'Azjol-Nerub', 'Azshara', 'Azuregos', 'Azuremyst', 'Baelgun', 'Balnazzar', 'Blackhand', 'Blackmoore', 'Blackrock', 'Blackscar', 'Blade\'s Edge', 'Bladefist', 'Bloodfeather', 'Bloodhoof', 'Bloodscalp', 'Blutkessel', 'Booty Bay', 'Borean Tundra', 'Boulderfist', 'Bronze Dragonflight', 'Bronzebeard', 'Burning Blade', 'Burning Legion', 'Burning Steppes', 'C\'Thun', 'Chamber of Aspects', 'Chants éternels', 'Cho\'gall', 'Chromaggus', 'Colinas Pardas', 'Confrérie du Thorium', 'Conseil des Ombres', 'Crushridge', 'Culte de la Rive noire', 'Daggerspine', 'Dalaran', 'Dalvengyr', 'Darkmoon Faire', 'Darksorrow', 'Darkspear', 'Das Konsortium', 'Das Syndikat', 'Deathguard', 'Deathweaver', 'Deathwing', 'Deepholm', 'Defias Brotherhood', 'Dentarg', 'Der Mithrilorden', 'Der Rat von Dalaran', 'Der abyssische Rat', 'Destromath', 'Dethecus', 'Die Aldor', 'Die Arguswacht', 'Die Nachtwache', 'Die Silberne Hand', 'Die Todeskrallen', 'Die ewige Wacht', 'Doomhammer	Normal', 'Draenor', 'Dragonblight', 'Dragonmaw', 'Drak\'thul', 'Drek\'Thar', 'Dun Modr', 'Dun Morogh	Normal', 'Dunemaul', 'Durotan', 'Earthen Ring', 'Echsenkessel', 'Eitrigg', 'Eldre\'Thalas', 'Elune', 'Emerald Dream', 'Emeriss', 'Eonar', 'Eredar', 'Eversong	Normal', 'Executus', 'Exodar	Normal', 'Festung der Stürme', 'Fordragon', 'Forscherliga', 'Frostmane', 'Frostmourne', 'Frostwhisper', 'Frostwolf', 'Galakrond', 'Garona', 'Garrosh', 'Genjuros', 'Ghostlands', 'Gilneas', 'Goldrinn', 'Gordunni', 'Gorgonnash', 'Greymane', 'Grim Batol', 'Grom', 'Gul\'dan', 'Hakkar', 'Haomarush', 'Hellfire', 'Hellscream', 'Howling Fjord', 'Hyjal', 'Illidan', 'Jaedenar', 'Kael\'thas', 'Karazhan', 'Kargath', 'Kazzak', 'Kel\'Thuzad', 'Khadgar', 'Khaz Modan	Normal', 'Khaz\'goroth', 'Kil\'jaeden', 'Kilrogg', 'Kirin Tor', 'Kor\'gall', 'Krag\'jin', 'Krasus', 'Kul Tiras', 'Kult der Verdammten', 'La Croisade écarlate', 'Laughing Skull', 'Les Clairvoyants', 'Les Sentinelles', 'Lich King', 'Lightbringer', 'Lightning\'s Blade', 'Lordaeron', 'Los Errantes', 'Lothar', 'Madmortem', 'Magtheridon', 'Mal\'Ganis', 'Malfurion', 'Malorne', 'Malygos', 'Mannoroth', 'Marécage de Zangar', 'Mazrigos', 'Medivh', 'Minahonda', 'Moonglade', 'Mug\'thol', 'Nagrand', 'Nathrezim', 'Naxxramas', 'Nazjatar', 'Nefarian', 'Nemesis', 'Neptulon', 'Ner\'zhul', 'Nera\'thor', 'Nethersturm', 'Nordrassil', 'Norgannon', 'Nozdormu', 'Onyxia', 'Outland', 'Perenolde', 'Pozzo dell\'Eternità', 'Proudmoore', 'Quel\'Thalas', 'Ragnaros', 'Rajaxx', 'Rashgarroth', 'Ravencrest', 'Ravenholdt', 'Razuvious', 'Rexxar', 'Runetotem', 'Sanguino', 'Sargeras', 'Saurfang', 'Scarshield Legion', 'Sen\'jin', 'Shadowsong', 'Shattered Halls', 'Shattered Hand', 'Shattrath', 'Shen\'dralar', 'Silvermoon', 'Sinstralis', 'Skullcrusher', 'Soulflayer', 'Spinebreaker', 'Sporeggar', 'Steamwheedle Cartel', 'Stormrage', 'Stormreaver', 'Stormscale', 'Sunstrider', 'Suramar', 'Sylvanas', 'Taerar', 'Talnivarr', 'Tarren Mill', 'Teldrassil', 'Temple noir', 'Terenas', 'Terokkar', 'Terrordar', 'The Maelstrom', 'The Sha\'tar', 'The Venture Co', 'Theradras', 'Thermaplugg', 'Thrall', 'Throk\'Feroth', 'Thunderhorn', 'Tichondrius', 'Tirion', 'Todeswache', 'Trollbane', 'Turalyon', 'Twilight\'s Hammer', 'Twisting Nether', 'Tyrande', 'Uldaman', 'Ulduar', 'Uldum', 'Un\'Goro', 'Varimathras', 'Vashj', 'Vek\'lor', 'Vek\'nilash', 'Vol\'jin', 'Wildhammer', 'Wrathbringer', 'Xavius', 'Ysera', 'Ysondre', 'Zenedar', 'Zirkel des Cenarius', 'Zul\'jin', 'Zuluhed',
    ];

    public function getDescription(): string
    {
        return 'Europe server list for Burning crusade classic with timezone Europe/Berlin';
    }

    public function up(Schema $schema): void
    {
        // VALUES (id, GameVersion, Region, Timezone, ServerName)

        if (!$timezone = $this->connection->executeQuery("SELECT `id` FROM timezone WHERE `name` = 'Europe/Berlin'")->fetchOne()) {
            echo 'Pas de timezone trouvé pour Europe/Berlin';
            return;
        }

        $count = 1;
        $sql = "INSERT INTO server VALUES ";

        foreach ($this->serversClassic as $serverName) {
            $sql .= "($count, 2, 2, $timezone, '" . addslashes($serverName) . "') ,";
            $count++;
        }

        foreach ($this->serversTBC as $serverName) {
            $sql .= "($count, 3, 2, $timezone, '" . addslashes($serverName) . "') ,";
            $count++;
        }

        foreach ($this->serversRetail as $serverName) {
            $sql .= "($count, 1, 2, $timezone, '" . addslashes($serverName) . "') ,";
            $count++;
        }

        $sql = substr($sql, 0, -1);

        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        // no down
    }
}
