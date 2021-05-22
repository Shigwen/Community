<?php

namespace App\Service\Raid;

use App\Entity\Role;
use App\Entity\Character;
use App\Entity\RaidCharacter;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class CreateRaidFromForm {

	/**
	 * @var EntityManagerInterface $em
	 */
	private $em;

	public function __construct(EntityManagerInterface $em)
    {
		$this->em = $em;
    }

	public function create(FormInterface $form, RaidCharacter $raidCharacter, User $user, array $datas)
	{
		$character = $this->em->getRepository(Character::class)->findOneBy([
			'id' => $datas['raidCharacter']['userCharacter'],
			'user' => $user,
		]);

		$role = $this->em->getRepository(Role::class)->findOneBy([
			'id' => $datas['raidCharacter']['role'],
		]);

		if (!$character || !$role) {
			throw $this->createNotFoundException('Une erreur est survenue');
		}

		$raid = $form->getData();
		$raid->setServer($character->getServer());

		$raidCharacter
			->setUserCharacter($character)
			->setRole($role);

		$this->em->persist($raid);
		$this->em->flush();
	}
}
