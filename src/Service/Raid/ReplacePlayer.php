<?php

namespace App\Service\Raid;

use App\Entity\RaidCharacter;
use Doctrine\ORM\EntityManagerInterface;

class ReplacePlayer
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Replace this character by the most-anciently-subscribed one with the same role and 
     * which subscription wasn't confirmed or rejected yet
     */
    public function replace(RaidCharacter $raidCharacterToReplace, $role)
    {
        $raidCharactersInWaitingForRole = $this->em->getRepository(RaidCharacter::class)->findBy([
            'raid' => $raidCharacterToReplace->getRaid(),
            'role' => $role,
            'status' => RaidCharacter::WAITING_CONFIRMATION
        ], ['createdAt' => 'ASC'], 1);

        if (empty($raidCharactersInWaitingForRole)) {
            return false;
        }

        $raidCharacter = $raidCharactersInWaitingForRole[0];
        $raidCharacter->setStatus(RaidCharacter::ACCEPT);
        $this->em->flush();

        return true;
    }
}
