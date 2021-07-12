<?php

namespace App\Twig;

use App\Entity\Raid;
use App\Entity\User;
use Twig\TwigFunction;
use App\Entity\RaidCharacter;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\EntityManagerInterface;

class RaidCharacterExtension extends AbstractExtension
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_raid_character', [$this, 'getRaidCharacter']),
        ];
    }

    public function getRaidCharacter(Raid $raid, User $user = null)
    {
        if ($user) {
            return $this->em->getRepository(RaidCharacter::class)->getOfUserFromRaid($raid, $user);
        }

        return $this->em->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid($raid);
    }
}
