<?php

namespace App\Twig;

use App\Entity\Raid;
use Twig\TwigFunction;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;

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
            new TwigFunction('get_raid_character_from_raidleader', [$this, 'getRaidCharacterFromRaidLeader']),
        ];
    }

    public function getRaidCharacterFromRaidLeader(Raid $raid)
    {
        return $this->em->getRepository(RaidCharacter::class)->getOfRaidLeaderFromRaid($raid);
    }
}
