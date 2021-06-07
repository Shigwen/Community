<?php

namespace App\Service\Raid;

use App\Entity\Raid;
use App\Entity\User;
use App\Entity\RaidCharacter;
use Doctrine\ORM\EntityManagerInterface;

class RaidCharacterFromUserAndRaid
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFromRaid(Raid $raid, User $user = null, bool $getCharacter = false)
    {
        $raidCharacter = $user
            ? $this->em->getRepository(RaidCharacter::class)->getOfUserFromRaid($raid, $user)
            : $this->em->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid($raid);

        if (!$raidCharacter) {
            return null;
        }

        return $getCharacter ? $raidCharacter->getUserCharacter() : $raidCharacter;
    }
}
