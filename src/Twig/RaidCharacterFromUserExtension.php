<?php

namespace App\Twig;

use App\Entity\Raid;
use App\Entity\User;
use Twig\TwigFunction;
use App\Service\Raid\RaidCharacterFromUserAndRaid;
use Twig\Extension\AbstractExtension;

class RaidCharacterFromUserExtension extends AbstractExtension
{
    private $raidCharacterService;

    public function __construct(RaidCharacterFromUserAndRaid $raidCharacterService)
    {
        $this->raidCharacterService = $raidCharacterService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_raid_character_from_user', [$this, 'getRaidCharacterFromUser']),
        ];
    }

    public function getRaidCharacterFromUser(Raid $raid, User $user = null, bool $getCharacter = false)
    {
        return $this->raidCharacterService->getFromRaid($raid, $user, $getCharacter);
    }
}
