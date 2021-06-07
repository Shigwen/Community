<?php

namespace App\Twig;

use App\Entity\Raid;
use App\Entity\User;
use Twig\TwigFunction;
use App\Entity\Character;
use App\Service\Raid\RaidCharacterFromUserAndRaid;
use Twig\Extension\AbstractExtension;

class RaidLeaderCharacterExtension extends AbstractExtension
{
    private $raidCharacterService;

    public function __construct(RaidCharacterFromUserAndRaid $raidCharacterService)
    {
        $this->raidCharacterService = $raidCharacterService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('is_raid_leader_character', [$this, 'isRaidLeaderCharacter']),
        ];
    }

    public function isRaidLeaderCharacter(Raid $raid, User $user, Character $character)
    {
        if ($raid->getUser() !== $user) {
            return false;
        }

        $raidCharacter = $this->raidCharacterService->getFromRaid($raid);

        return $raidCharacter->getUserCharacter() === $character;
    }
}
