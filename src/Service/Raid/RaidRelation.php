<?php

namespace App\Service\Raid;

use App\Entity\Raid;
use App\Entity\Role;
use App\Entity\Character;
use App\Entity\RaidCharacter;
use Doctrine\ORM\EntityManagerInterface;

class RaidRelation {

	/**
	 * @var EntityManagerInterface $em
	 */
	private $em;

	public function __construct(EntityManagerInterface $em)
    {
		$this->em = $em;
    }

	public function addCharacterAndServerToRaid(Raid $raid, RaidCharacter $raidCharacter, array $datas)
	{	
		$character = $this->em->getRepository(Character::class)->findOneBy([
			'id' => $datas['raidCharacter']['userCharacter'],
			'user' => $raid->getUser(),
		]);

		$role = $this->em->getRepository(Role::class)->findOneBy([
			'id' => $datas['raidCharacter']['role'],
		]);

		if (!$character || !$role) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$raid->setServer($character->getServer());
		$raidCharacter
			->setUserCharacter($character)
			->setRole($role);
		
		return $raid;
	}
}
