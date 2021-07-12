<?php

namespace App\Service\Raid;

use App\Entity\Role;
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
     * Remplace le personnage donné par le plus ancien personnage en 
     * attente d'inscription pour le même role dans le raid
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
